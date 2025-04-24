<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UploadNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $link;

    public function __construct($link) {
        $this->link = $link;
    }

    public function build() {
        return $this->subject('Your file upload is ready')
            ->view('emails.upload_notification');
    }
}
