<?php

namespace App\Mail;

use App\Models\Quote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $quote;

    /**
     * Create a new message instance.
     */
    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = match($this->quote->status) {
            'under_review' => '🎉 جاهز لفعاليتك؟ عرضك صار بين يديك! - ' . $this->quote->quote_number,
            'approved' => '🎉 جاهز لفعاليتك؟ عرضك صار بين يديك! - ' . $this->quote->quote_number,
            'rejected' => 'تحديث بخصوص عرض السعر - ' . $this->quote->quote_number,
            default => '🎉 جاهز لفعاليتك؟ عرضك صار بين يديك! - ' . $this->quote->quote_number,
        };

        return $this->subject($subject)
                    ->view('emails.quote')
                    ->with(['quote' => $this->quote]);
    }
}
