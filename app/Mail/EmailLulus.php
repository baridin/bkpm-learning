<?php

namespace App\Mail;
  
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailLulus extends Mailable
{
    use Queueable, SerializesModels;

    public $nilai;
    public $diklat;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($diklat,$nilai)
    {
        //
        $this->diklat = $diklat;
        $this->nilai = $nilai;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $diklat = $this->diklat;
        $nilai = $this->nilai;
        return $this->from(env('MAIL_USERNAME'))
        ->subject('Info Kelulusan')
        ->view('vendor.mail.lulus', compact(['diklat','nilai']));
    }
}
