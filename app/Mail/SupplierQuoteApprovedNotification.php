<?php

namespace App\Mail;

use App\Models\Quote;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SupplierQuoteApprovedNotification extends Mailable
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
            subject: '🔥 فرصة عمل جديدة - عرض سعر موافق عليه',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.supplier-quote-approved',
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
