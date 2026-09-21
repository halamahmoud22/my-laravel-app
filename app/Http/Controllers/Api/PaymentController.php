<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use OpenApi\Attributes as OA;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    #[OA\Post(
        path: '/api/payments/intent',
        summary: 'Create a Stripe PaymentIntent',
        description: 'Creates a PaymentIntent and returns its client_secret so the frontend can confirm payment via Stripe.js.',
        tags: ['Payments'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['order_id', 'currency'],
                properties: [
                    new OA\Property(property: 'order_id', type: 'integer', example: 1, description: 'ID of the order to charge'),
                    new OA\Property(property: 'currency', type: 'string', minLength: 3, maxLength: 3, example: 'usd'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'PaymentIntent created successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'client_secret', type: 'string', example: 'pi_3P.._secret_XXXX'),
                        new OA\Property(property: 'id', type: 'string', example: 'pi_3P...'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error or Stripe API error',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'order not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
        ]
    )]
    public function createPaymentIntent(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'currency' => 'required|string|size:3',
        ]);
        $order = Order::findOrFail($validated['order_id']);
        $amountInCents = (int) round($order->total * 100);
        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount'   => $amountInCents,
                'currency' => $validated['currency'],
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            return response()->json([
                'success'      => true,
                'client_secret' => $intent->client_secret,
                'id'           => $intent->id,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe PaymentIntent error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    #[OA\Post(
        path: '/api/payments/charge',
        summary: 'Create and immediately confirm a Stripe charge',
        description: 'Creates a PaymentIntent and confirms it in the same request using an existing payment_method (no client-side confirmation step needed).',
        tags: ['Payments'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['order_id', 'currency', 'payment_method'],
                properties: [
                    new OA\Property(property: 'order_id', type: 'integer', example: 1, description: 'ID of the order to charge'),
                    new OA\Property(property: 'currency', type: 'string', minLength: 3, maxLength: 3, example: 'usd'),
                    new OA\Property(property: 'payment_method', type: 'string', example: 'pm_card_visa', description: 'Stripe PaymentMethod ID'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Charge created and confirmed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status', type: 'string', example: 'succeeded'),
                        new OA\Property(property: 'id', type: 'string', example: 'pi_3P...'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Validation error or Stripe API error (e.g. card declined)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'order not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: false),
                        new OA\Property(property: 'message', type: 'string'),
                    ]
                )
            ),
        ]
    )]
    public function charge(Request $request)
    {
        $validated = $request->validate([
            'order_id'       => 'required|exists:orders,id',
            'currency'       => 'required|string|size:3',
            'payment_method' => 'required|string',
        ]);

        $order = Order::findOrFail($validated['order_id']);
        $this->authorize('view', $order);

        $amountInCents = (int) round($order->total * 100);

        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount'         => $amountInCents,
                'currency'       => $validated['currency'],
                'payment_method' => $validated['payment_method'],
                'confirm'        => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);

            // Map Stripe's status vocabulary onto your fixed enum
            $paymentStatus = $intent->status === 'succeeded' ? 'completed' : 'failed';

            \DB::transaction(function () use ($order, $intent, $validated, $paymentStatus) {
                \App\Models\Payment::create([
                    'order_id'       => $order->id,
                    'amount'         => $order->total,
                    'payment_method' => $validated['payment_method'],
                    'status'         => $paymentStatus,
                    'transaction_id' => $intent->id,
                ]);

                if ($paymentStatus === 'completed') {
                    $order->update(['status' => 'paid']);
                }
            });

            return response()->json([
                'success' => true,
                'status'  => $intent->status,
                'id'      => $intent->id,
            ]);
        } catch (ApiErrorException $e) {
            Log::error('Stripe charge error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

/**
 * TODO: Build a Stripe webhook handler (e.g. POST /api/webhooks/stripe) to
 * complete this flow. createPaymentIntent() only creates a 'pending' Payment
 * row - it never finds out if the payment actually succeeded, because
 * confirmation happens client-side via Stripe.js, outside this request.
 *
 * The webhook needs to:
 *  - Be a PUBLIC route (no auth:sanctum) - Stripe calls it directly, not a logged-in user
 *  - Verify the Stripe-Signature header against your webhook signing secret,
 *    to confirm the request genuinely came from Stripe and not a forged call
 *  - Listen for 'payment_intent.succeeded' and 'payment_intent.payment_failed' events
 *  - Look up the Payment row by transaction_id (Stripe's PaymentIntent id)
 *  - Update Payment.status to 'completed'/'failed', and on success, set
 *    Order.status to 'paid' - mirroring what charge() already does inline
 */
}
