<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'gateway',
        'gateway_payment_id',
        'gateway_transaction_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'paid_at',
        'failed_at',
        'refunded_at',
        'description',
        'failure_reason',
        'metadata',
        'invoice_number',
        'invoice_url',
        'refund_amount',
        'refund_reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getMethodAttribute()
    {
        return $this->payment_method;
    }

    public function setMethodAttribute($value)
    {
        $this->attributes['payment_method'] = $value;
    }

    public function getProviderAttribute()
    {
        return $this->gateway;
    }

    public function setProviderAttribute($value)
    {
        $this->attributes['gateway'] = $value;
    }

    public function getProviderReferenceAttribute()
    {
        return $this->gateway_transaction_id ?: $this->gateway_payment_id;
    }

    public function setProviderReferenceAttribute($value)
    {
        $this->attributes['gateway_transaction_id'] = $value;
    }

    public function getCapturedAtAttribute()
    {
        return $this->paid_at;
    }

    public function setCapturedAtAttribute($value)
    {
        $this->attributes['paid_at'] = $value;
    }

    public function getNotesAttribute()
    {
        return $this->description;
    }

    public function setNotesAttribute($value)
    {
        $this->attributes['description'] = $value;
    }
}
