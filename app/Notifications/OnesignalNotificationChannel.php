<?php

namespace App\Notifications;

use App\Onesignal\Onesignal;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;


class OnesignalNotificationChannel
{

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $data = $notification->toOnesignal($notifiable);
        $message = $data['message'];
        $link = $data['link'];

        $payloads = DB::table('sessions')->where('user_id', $notifiable->id)->select(['id', 'payload'])->get();
        $playerIds = $payloads->map(function ($item) {
            $payloadData = unserialize(base64_decode($item->payload));
            return $payloadData['playerId'];
        })->filter(function ($item) {
            return $item != null;
        });

        if ($playerIds->isNotEmpty()) {
            Onesignal::send($message, $playerIds->toArray(), $link);
        }
    }
}
