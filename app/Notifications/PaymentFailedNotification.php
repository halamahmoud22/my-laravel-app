<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class PaymentFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Payment $payment)
    {
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database'];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'payment_id'     => $this->payment->id,
            'order_id'       => $this->payment->order_id,
            'amount'         => (float) $this->payment->amount,
            'transaction_id' => $this->payment->transaction_id,
            'message'        => "Payment for Order #{$this->payment->order_id} failed. Please try again or update your payment method.",
        ]);
    }

    /**
     * Broadcast event type.
     */
    public function broadcastType(): string
    {
        return 'PaymentFailedNotification';
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payment_id'     => $this->payment->id,
            'order_id'       => $this->payment->order_id,
            'amount'         => (float) $this->payment->amount,
            'transaction_id' => $this->payment->transaction_id,
            'message'        => "Payment for Order #{$this->payment->order_id} failed. Please try again or update your payment method.",
        ];
    }
}
