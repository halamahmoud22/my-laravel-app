<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Events\PaymentConfirmed;
use App\Events\PaymentFailed;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogActivity implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderPlaced|PaymentConfirmed|PaymentFailed $event): void
    {
        [$order, $action, $description] = match (true) {
            $event instanceof OrderPlaced => [
                $event->order,
                'order_placed',
                "Order #{$event->order->id} placed",
            ],
            $event instanceof PaymentConfirmed => [
                $event->payment->order,
                'payment_confirmed',
                "Payment {$event->payment->transaction_id} confirmed via webhook for Order #{$event->payment->order_id}",
            ],
            $event instanceof PaymentFailed => [
                $event->payment->order,
                'payment_failed',
                "Payment {$event->payment->transaction_id} failed via webhook for Order #{$event->payment->order_id}",
            ],
        };

        ActivityLog::create([
            'user_id'     => $order->user_id,
            'action'      => $action,
            'description' => $description,
        ]);
    }
}
