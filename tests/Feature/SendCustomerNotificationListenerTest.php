<?php

namespace Tests\Feature;

use App\Events\OrderPlaced;
use App\Events\PaymentConfirmed;
use App\Events\PaymentFailed;
use App\Listeners\SendCustomerNotification;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\PaymentFailedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendCustomerNotificationListenerTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_placed_sends_live_only_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total'   => 150.00,
            'status'  => 'pending',
        ]);

        $listener = new SendCustomerNotification();
        $listener->handle(new OrderPlaced($order));

        Notification::assertSentTo(
            $user,
            OrderPlacedNotification::class,
            function (OrderPlacedNotification $notification) use ($user, $order) {
                $channels = $notification->via($user);
                $this->assertContains('broadcast', $channels);
                $this->assertContains('database', $channels);
                $this->assertNotContains('mail', $channels);

                $broadcastData = $notification->toBroadcast($user)->data;
                $this->assertEquals($order->id, $broadcastData['order_id']);
                $this->assertEquals(150.00, $broadcastData['total']);
                return true;
            }
        );
    }

    public function test_payment_confirmed_sends_live_and_mail_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create(['name' => 'Alice']);
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
            'transaction_id' => 'txn_conf_789',
        ]);

        $listener = new SendCustomerNotification();
        $listener->handle(new PaymentConfirmed($payment));

        Notification::assertSentTo(
            $user,
            PaymentConfirmedNotification::class,
            function (PaymentConfirmedNotification $notification) use ($user, $payment, $order) {
                $channels = $notification->via($user);
                $this->assertContains('broadcast', $channels);
                $this->assertContains('database', $channels);
                $this->assertContains('mail', $channels);

                $broadcastData = $notification->toBroadcast($user)->data;
                $this->assertEquals($payment->id, $broadcastData['payment_id']);
                $this->assertEquals($order->id, $broadcastData['order_id']);

                $mailData = $notification->toMail($user);
                $this->assertStringContainsString("Payment Confirmed - Order #{$order->id}", $mailData->subject);
                $this->assertStringContainsString('txn_conf_789', implode(' ', $mailData->introLines));
                return true;
            }
        );
    }

    public function test_payment_failed_sends_live_only_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total'   => 200.00,
            'status'  => 'pending',
        ]);

        $payment = Payment::create([
            'order_id'       => $order->id,
            'amount'         => 200.00,
            'payment_method' => 'card',
            'status'         => 'failed',
            'transaction_id' => 'txn_fail_123',
        ]);

        $listener = new SendCustomerNotification();
        $listener->handle(new PaymentFailed($payment));

        Notification::assertSentTo(
            $user,
            PaymentFailedNotification::class,
            function (PaymentFailedNotification $notification) use ($user, $payment) {
                $channels = $notification->via($user);
                $this->assertContains('broadcast', $channels);
                $this->assertContains('database', $channels);
                $this->assertNotContains('mail', $channels);

                $broadcastData = $notification->toBroadcast($user)->data;
                $this->assertEquals($payment->id, $broadcastData['payment_id']);
                $this->assertEquals('txn_fail_123', $broadcastData['transaction_id']);
                return true;
            }
        );
    }

    public function test_all_notifications_are_persisted_to_database(): void
    {
        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'total'   => 50.00,
            'status'  => 'pending',
        ]);

        $payment = Payment::create([
            'order_id'       => $order->id,
            'amount'         => 50.00,
            'payment_method' => 'card',
            'status'         => 'completed',
            'transaction_id' => 'txn_persist_1',
        ]);

        $failedPayment = Payment::create([
            'order_id'       => $order->id,
            'amount'         => 50.00,
            'payment_method' => 'card',
            'status'         => 'failed',
            'transaction_id' => 'txn_persist_fail',
        ]);

        $user->notify(new OrderPlacedNotification($order));
        $user->notify(new PaymentConfirmedNotification($payment));
        $user->notify(new PaymentFailedNotification($failedPayment));

        $this->assertCount(3, $user->notifications);
        $this->assertTrue($user->notifications->contains(fn ($n) => $n->type === OrderPlacedNotification::class));
        $this->assertTrue($user->notifications->contains(fn ($n) => $n->type === PaymentConfirmedNotification::class));
        $this->assertTrue($user->notifications->contains(fn ($n) => $n->type === PaymentFailedNotification::class));
    }
}
