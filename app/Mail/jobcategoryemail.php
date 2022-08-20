<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class jobcategoryemail extends Mailable implements  ShouldQueue
{
    use Queueable, SerializesModels;
    public $jobsalertmails; 

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($jobsalertmails)
    { 
       
        $this->jobsalertmails=$jobsalertmails;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    { 
         
        return $this->view('emails.jobcategoryemail');

    }
}
