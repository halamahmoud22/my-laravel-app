<?php

namespace Tests\Feature;

use App\Events\OrderPlaced;
use App\Events\PaymentConfirmed;
use App\Events\PaymentFailed;
use App\Listeners\LogActivity;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class LogActivityListenerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::connection('logs')->hasTable('activity_logs')) {
            $this->artisan('migrate', ['--database' => 'logs']);
        } else {
            ActivityLog::truncate();
        }
    }

    public function test_log_activity_handles_order_placed(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total'   => 120.00,
            'status'  => 'pending',
        ]);

        $listener = new LogActivity();
        $listener->handle(new OrderPlaced($order));

        $this->assertDatabaseHas('activity_logs', [
            'user_id'     => $user->id,
            'action'      => 'order_placed',
            'description' => "Order #{$order->id} placed",
        ], 'logs');
    }

    public function test_log_activity_handles_payment_confirmed(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total'   => 250.00,
            'status'  => 'paid',
        ]);

        $payment = Payment::create([
            'order_id'       => $order->id,
            'amount'         => 250.00,
            'payment_method' => 'card',
            'status'         => 'completed',
            'transaction_id' => 'txn_conf_123',
        ]);

        $listener = new LogActivity();
        $listener->handle(new PaymentConfirmed($payment));

        $this->assertDatabaseHas('activity_logs', [
            'user_id'     => $user->id,
            'action'      => 'payment_confirmed',
            'description' => "Payment txn_conf_123 confirmed via webhook for Order #{$order->id}",
        ], 'logs');
    }

    public function test_log_activity_handles_payment_failed(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total'   => 300.00,
            'status'  => 'pending',
        ]);

        $payment = Payment::create([
            'order_id'       => $order->id,
            'amount'         => 300.00,
            'payment_method' => 'card',
            'status'         => 'failed',
            'transaction_id' => 'txn_fail_456',
        ]);

        $listener = new LogActivity();
        $listener->handle(new PaymentFailed($payment));

        $this->assertDatabaseHas('activity_logs', [
            'user_id'     => $user->id,
            'action'      => 'payment_failed',
            'description' => "Payment txn_fail_456 failed via webhook for Order #{$order->id}",
        ], 'logs');
    }
}
