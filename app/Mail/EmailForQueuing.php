<?php

namespace App\Mail;
  
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailForQueuing extends Mailable
{
    use Queueable, SerializesModels;

    public $mail;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mail)
    {
        //
        $this->mail = $mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail = $this->mail;
        return $this->from(env('MAIL_USERNAME'))
        ->subject('Ujian Susulan')
        ->view('vendor.mail.susulan', compact('mail'));
    }
}
