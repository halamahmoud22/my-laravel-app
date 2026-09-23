<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderConfirmationMail;

class SendOrderConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, SerializesModels;

    public function __construct(public Order $order)
    {
        //
    }

    public function handle(): void
    {
        Mail::to($this->order->user->email)->send(new OrderConfirmationMail($this->order));
    }
}
