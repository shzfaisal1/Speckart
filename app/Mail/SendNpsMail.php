<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendNpsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sale;
    public $link;

    /**
     * Create a new message instance.
     *
     * @param  object|array  $sale  // a sale row (stdClass from query or Eloquent model)
     */
    public function __construct($sale)
    {
        $this->sale = $sale;

        // Build a simple HMAC token so the nps-form can verify the request.
        // Uses APP_KEY as secret (available via config('app.key')).
        $secret = config('app.key') ?: env('APP_KEY');
        $payload = ($sale->sale_id ?? '') . '|' . ($sale->email_id ?? '') . '|' . ($sale->sale_date ?? '');
        $token = hash_hmac('sha256', $payload, $secret);

        // NPS URL (change path as per your route)
        $this->link = url('/nps-form/' . ($sale->sale_id ?? '') . '?token=' . $token);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'We value your feedback',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.nps',
            with: [
                'sale' => $this->sale,
                'link' => $this->link,
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