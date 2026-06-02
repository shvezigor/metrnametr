<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Mail\OrderConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 2;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderCreated  $event
     * @return void
     */
    public function handle(OrderCreated $event)
    {

        $order = $event->order;

        foreach ([config('common.mailForCompany'), config('common.mailForAdmin')] as $recipient) {
            Mail::to($recipient)->queue(new OrderConfirmed($order));
        }
    }
}
