<?php

namespace App\Listeners;

use App\Models\Sms;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SmsForwarderEmailSentListner
{
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
     * @param object $event
     * @return void
     */
    public function handle(MessageSent $event)
    {
        if (key_exists('smsId', $event->data)) {
            $messageId = '<'.$event->sent->getMessageId().'>';
            Sms::whereId($event->data['smsId'])->update(['email_sent' => true, 'email_id' => $messageId]);
        }
    }
}
