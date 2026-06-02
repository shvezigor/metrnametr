<?php

namespace App\Listeners;

use App\Events\Subscribe;
use App\Mail\ClientSubscribe;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendSubscribeNotification implements ShouldQueue
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
     * @param  Subscribe  $event
     * @return void
     */
    public function handle(Subscribe $event)
    {
        $subscriber = $event->subscriber;

        foreach ([config('common.mailForCompany'), config('common.mailForAdmin')] as $recipient) {
            Mail::to($recipient)->queue(new ClientSubscribe($subscriber));
        }
    }
}
