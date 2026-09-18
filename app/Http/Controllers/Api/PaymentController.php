<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    protected StripeClient $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function createPaymentIntent(Request $request)
    {
        $validated = $request->validate([
            'amount'   => 'required|integer|min:50',
            'currency' => 'required|string|size:3',
        ]);

        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount'   => $validated['amount'],
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

    public function charge(Request $request)
    {
        $validated = $request->validate([
            'amount'          => 'required|integer|min:50',
            'currency'        => 'required|string|size:3',
            'payment_method'  => 'required|string',
        ]);

        try {
            $intent = $this->stripe->paymentIntents->create([
                'amount'         => $validated['amount'],
                'currency'       => $validated['currency'],
                'payment_method' => $validated['payment_method'],
                'confirm'        => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
            ]);

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
}
