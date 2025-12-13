<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'quote_id',
        'booking_id',
        'amount',
        'currency',
        'method',
        'status',
        'provider',
        'provider_reference',
        'notes',
        'metadata',
        'captured_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'captured_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
