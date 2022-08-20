<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class JobNumberAlertEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $scheduledCount, $unscheduledCount, $category;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $scheduledCount, $unscheduledCount, $category)
    {
        $this->subject = $subject;
        $this->from(env('MAIL_FROM_ADDRESS'),'Scheduled Jobs Alert');
        $this->scheduledCount = $scheduledCount;
        $this->unscheduledCount = $unscheduledCount;
        $this->category = $category;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.jobNumberAlert');
    }
}
