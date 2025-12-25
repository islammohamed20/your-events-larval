<?php

namespace App\Mail;

use App\Models\Booking;
use App\Models\Supplier;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public $supplier;

    /**
     * Create a new message instance.
     */
    public function __construct(Booking $booking, Supplier $supplier)
    {
        $this->booking = $booking;
        $this->supplier = $supplier;
    }

    /**
     * Get the message envelope.
     */
    public function envelope()
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: '🔥 حجز جديد متاح للتنافس - '.$this->booking->booking_reference,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content()
    {
        return new \Illuminate\Mail\Mailables\Content(
            view: 'emails.booking-notification',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments()
    {
        return [];
    }
}
