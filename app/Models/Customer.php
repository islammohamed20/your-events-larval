<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'company_name',
        'company_registration',
        'company_address',
        'company_phone',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'region',
        'notes',
        'status',
        'is_verified',
        'registered_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'registered_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقات
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id', 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id', 'user_id');
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class, 'user_id', 'user_id');
    }

    /**
     * Helper Methods
     */
    public function getFullNameAttribute()
    {
        return $this->user->name ?? 'Unknown';
    }

    public function getEmailAttribute()
    {
        return $this->user->email ?? '';
    }

    public function getPhoneNumberAttribute()
    {
        return $this->phone ?? $this->user->phone ?? 'غير متوفر';
    }
}
