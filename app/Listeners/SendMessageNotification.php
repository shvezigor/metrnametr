<?php

namespace App\Listeners;

use App\Events\MessageCreated;
use App\Mail\SendMessageRequest;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendMessageNotification implements ShouldQueue
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
     * @param  MessageCreated  $event
     * @return void
     */
    public function handle(MessageCreated $event)
    {

        $message = $event->message;

        foreach ([config('common.mailForCompany'), config('common.mailForAdmin')] as $recipient) {
            Mail::to($recipient)->queue(new SendMessageRequest($message));
        }
    }
}
