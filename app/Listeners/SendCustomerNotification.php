<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Events\PaymentConfirmed;
use App\Events\PaymentFailed;
use App\Notifications\OrderPlacedNotification;
use App\Notifications\PaymentConfirmedNotification;
use App\Notifications\PaymentFailedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCustomerNotification implements ShouldQueue
{
    /**
     * Handle the event.
     */
    public function handle(OrderPlaced|PaymentConfirmed|PaymentFailed $event): void
    {
        match (true) {
            $event instanceof OrderPlaced => 
                $event->order->user?->notify(new OrderPlacedNotification($event->order)),

            $event instanceof PaymentConfirmed => 
                $event->payment->order?->user?->notify(new PaymentConfirmedNotification($event->payment)),

            $event instanceof PaymentFailed => 
                $event->payment->order?->user?->notify(new PaymentFailedNotification($event->payment)),
        };
    }
}
