<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TapPayment extends Model
{
    protected $fillable = [
        'payment_id',
        'booking_id',
        'quote_id',
        'tap_charge_id',
        'tap_transaction_id',
        'amount',
        'currency',
        'status',
        'customer_email',
        'customer_phone',
        'charge_data',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'charge_data' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }
}
