<?php

namespace App\Mail;

use App\Models\Quote;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierAcceptedQuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;

    public $supplier;

    /**
     * Create a new message instance.
     */
    public function __construct(Quote $quote, Supplier $supplier)
    {
        $this->quote = $quote;
        $this->supplier = $supplier;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'قبول عرض السعر من المورد - '.($this->quote->quote_number ?? 'عرض سعر'),
            mailer: 'sales',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.supplier-accepted-quote',
            with: [
                'quote' => $this->quote,
                'supplier' => $this->supplier,
            ]
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
