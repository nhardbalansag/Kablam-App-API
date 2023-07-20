<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Support\Facades\Auth;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $EmailContent = "";
    public $FullName = "";
    public $EmailAddress = "";

    /**
     * Create a new message instance.
     */
    public function __construct($request)
    {
        $this->EmailContent = $request->message;
        $this->FullName = Auth::user()->first_name . " " . Auth::user()->last_name;
        $this->EmailAddress = Auth::user()->email;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('kablam.development@gmail.com', 'KABLAM CONTACT US (LOCAL TEST)'),
            subject: 'KABLAM (Development)',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.test',
            with: [
                'message' => $this->EmailContent,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
