<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentConfirmedNotification extends Notification implements ShouldQueue
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
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['broadcast', 'database', 'mail'];
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
            'message'        => "Payment of \${$this->payment->amount} for Order #{$this->payment->order_id} was successful.",
        ]);
    }

    /**
     * Broadcast event type.
     */
    public function broadcastType(): string
    {
        return 'PaymentConfirmedNotification';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Payment Confirmed - Order #{$this->payment->order_id}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your payment of \${$this->payment->amount} for Order #{$this->payment->order_id} was successful.")
            ->line("Transaction ID: {$this->payment->transaction_id}")
            ->action('View Order', url("/orders/{$this->payment->order_id}"))
            ->line('Thank you for using our application!');
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
            'message'        => "Payment of \${$this->payment->amount} for Order #{$this->payment->order_id} was successful.",
        ];
    }
}
