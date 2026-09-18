<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Stripe\StripeClient;
use Stripe\Service\PaymentIntentService;
use Mockery;

class PaymentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_payment_intent_returns_client_secret()
    {
        $user = User::factory()->create();

        $fakeIntent = (object) [
            'id' => 'pi_fake_123',
            'client_secret' => 'pi_fake_123_secret_abc',
        ];

        $paymentIntentServiceMock = Mockery::mock(PaymentIntentService::class);
        $paymentIntentServiceMock->shouldReceive('create')
            ->once()
            ->andReturn($fakeIntent);

        $stripeClientMock = Mockery::mock(StripeClient::class);
        $stripeClientMock->shouldReceive('getService')
            ->with('paymentIntents')
            ->andReturn($paymentIntentServiceMock);

        $this->app->instance(StripeClient::class, $stripeClientMock);

        $response = $this->actingAs($user)
            ->postJson('/api/payments/intent', [
                'amount'   => 1000,
                'currency' => 'usd',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'client_secret' => 'pi_fake_123_secret_abc',
            ]);
    }

    public function test_create_payment_intent_fails_validation()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/payments/intent', [
                'amount' => 'not-a-number',
            ]);

        $response->assertStatus(422);
    }
}
