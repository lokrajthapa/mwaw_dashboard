<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Mime\Email;

class SmsForwarderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $smsData, $smsId, $emergency, $mapsLink, $mapsImageUrl, $references, $hasUpdate;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($smsId, $smsData, $mapsLink, $mapsImageUrl, $references, $emergency = false)
    {
        $this->smsData = $smsData;
        $this->smsId = $smsId;
        $this->emergency = $emergency;
        $this->mapsLink = $mapsLink;
        $this->mapsImageUrl = $mapsImageUrl;
        $this->references = $references;
        $this->hasUpdate = !empty($smsData['updates']['value']);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = '';
        if ($this->references) {
            $subject .= 'Re: ';
        }
        $subject .= 'ALLFIX';

        if ($this->smsData['type'] == 'reminder') {
            $subject .= '-Reminder';

        } elseif ($this->smsData['type'] == 'raw') {
            $subject .= '-Raw Sms';
        }

        if (key_exists('address', $this->smsData)) {
            $subject .= '-' . $this->smsData['address']['value'];
        }

        if (key_exists('name', $this->smsData)) {
            $subject .= '-' . $this->smsData['name']['value'];
        }
        $name = $this->emergency ? 'Emergency - SMS to Email' : 'SMS to Email';

        unset($this->smsData['type']);
        return $this->subject($subject)->from('programming@manwithawrench.com', $name)
            ->withSymfonyMessage(function (Email $message) {

                if ($this->references) {
                    $referencesString = implode(' ', array_reverse($this->references));
                    $message->getHeaders()->addTextHeader('References', $referencesString)
                        ->addTextHeader('In-Reply-To', $this->references[0]);
                }

            })->view('emails.SmsForwarder');
    }
}
