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
use App\Events\PaymentConfirmed;
use App\Events\PaymentFailed;

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
        $this->authorize('view', $order);

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be paid again.',
            ], 422);
        }

        $amountInCents = (int) round($order->total * 100);

        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount'   => $amountInCents,
                'currency' => $validated['currency'],
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'metadata' => ['order_id' => $order->id],
            ]);

            Payment::create([
                'order_id'       => $order->id,
                'amount'         => $order->total,
                'payment_method' => 'card',
                'status'         => 'pending',
                'transaction_id' => $intent->id,
            ]);

            return response()->json([
                'success'       => true,
                'client_secret' => $intent->client_secret,
                'id'            => $intent->id,
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
        summary: 'Confirm a PaymentIntent (Charge)',
        description: 'Confirms a PaymentIntent to process the payment. If successful (or failed), Stripe will send a webhook. You can use test payment methods like pm_card_visa or pm_card_chargeCustomerFail.',
        tags: ['Payments'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['payment_intent_id', 'payment_method'],
                properties: [
                    new OA\Property(property: 'payment_intent_id', type: 'string', example: 'pi_3P...'),
                    new OA\Property(property: 'payment_method', type: 'string', example: 'pm_card_visa', description: 'Stripe payment method ID, e.g. pm_card_visa for success, pm_card_chargeCustomerFail for failure.'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Payment confirmed successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'status', type: 'string', example: 'succeeded'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Payment failed or validation error',
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
            'payment_intent_id' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        try {
            $intent = $this->stripe->paymentIntents->confirm(
                $validated['payment_intent_id'],
                [
                    'payment_method' => $validated['payment_method'],
                    'return_url' => url('/'), // Some payment methods require a return_url
                ]
            );

            // If the payment fails synchronously, Stripe API throws an exception or returns a specific status
            if ($intent->status === 'requires_action' || $intent->status === 'requires_source_action') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment requires additional action.',
                    'status' => $intent->status
                ], 422);
            }

            if ($intent->status === 'succeeded') {
                $payment = Payment::where('transaction_id', $validated['payment_intent_id'])->first();
                if ($payment && $payment->status !== 'completed') {
                    DB::transaction(function () use ($payment) {
                        $payment->update(['status' => 'completed']);
                        $payment->order->update(['status' => 'paid']);
                        event(new \App\Events\PaymentConfirmed($payment));
                    });
                }
            }

            return response()->json([
                'success' => true,
                'status' => $intent->status,
                'intent' => $intent
            ]);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error('Stripe charge error', ['error' => $e->getMessage()]);

            // If we have the payment intent ID, let's mark it failed in our DB manually
            // since local testing often misses webhooks
            $payment = Payment::where('transaction_id', $validated['payment_intent_id'])->first();
            if ($payment && $payment->status !== 'completed') {
                $payment->update(['status' => 'failed']);
                event(new \App\Events\PaymentFailed($payment));
            }

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    #[OA\Post(
        path: '/api/webhooks/stripe',
        summary: 'Handle incoming Stripe webhook events',
        description: 'Public endpoint called directly by Stripe (not by app users) to confirm the real outcome of a payment. Verifies the Stripe-Signature header against the webhook signing secret, then updates the matching Payment and Order records based on the event type (payment_intent.succeeded / payment_intent.payment_failed).',
        tags: ['Payments'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Raw Stripe Event payload, signed via the Stripe-Signature header.',
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'id', type: 'string', example: 'evt_1P...'),
                    new OA\Property(property: 'type', type: 'string', example: 'payment_intent.succeeded'),
                    new OA\Property(property: 'data', type: 'object'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Event received and processed (or ignored if not a handled type)',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'received', type: 'boolean', example: true),
                    ]
                )
            ),
            new OA\Response(
                response: 400,
                description: 'Invalid payload or signature verification failed',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string'),
                    ]
                )
            ),
        ]
    )]
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Stripe webhook: invalid payload');
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Stripe webhook: invalid signature');
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        $intent = $event->data->object;

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->markPaymentCompleted($intent);
                break;

            case 'payment_intent.payment_failed':
                $this->markPaymentFailed($intent);
                break;
        }

        return response()->json(['received' => true]);
    }

    protected function markPaymentCompleted($intent)
    {
        DB::transaction(function () use ($intent) {
            $payment = Payment::where('transaction_id', $intent->id)->first();

            if (! $payment || $payment->status === 'completed') {
                return; // not found, or already processed - avoid double-updating
            }
            $payment->update(['status' => 'completed']);
            $payment->order->update(['status' => 'paid']);

            event(new PaymentConfirmed($payment));
        });
    }

    protected function markPaymentFailed($intent)
    {
        $payment = Payment::where('transaction_id', $intent->id)->first();

        if ($payment && $payment->status !== 'completed') {
            $payment->update(['status' => 'failed']);

            event(new PaymentFailed($payment));
        }
    }
}
