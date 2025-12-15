<?php

namespace App\Mail;

use App\Models\CompetitiveOrder;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $supplier;

    public function __construct(CompetitiveOrder $order, Supplier $supplier)
    {
        $this->order = $order;
        $this->supplier = $supplier;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🔥 فرصة عمل جديدة - طلب عاجل #' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.competitive-order-notification',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
