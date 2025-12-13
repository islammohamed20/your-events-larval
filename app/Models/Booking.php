<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quote_id',
        'package_id',
        'service_id',
        'supplier_id',
        'activity_name',
        'client_name',
        'client_email',
        'client_phone',
        'event_date',
        'event_location',
        'guests_count',
        'special_requests',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_notes',
        'status',
        'booking_reference',
    ];

    protected $casts = [
        'event_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Generate booking reference
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($booking) {
            $booking->booking_reference = 'YE-' . strtoupper(Str::random(16));
        });

        // Logging events
        static::created(function ($booking) {
            \App\Models\ActivityLog::record($booking, 'created', 'تم إنشاء حجز جديد', [
                'status' => $booking->status,
                'total_amount' => $booking->total_amount,
                'booking_reference' => $booking->booking_reference,
            ]);
        });

        static::updated(function ($booking) {
            $changes = $booking->getChanges();
            // Avoid logging only timestamps updates with no meaningful changes
            unset($changes['updated_at']);
            if (!empty($changes)) {
                \App\Models\ActivityLog::record($booking, 'updated', 'تم تعديل الحجز', [
                    'changes' => $changes,
                ]);
            }
        });
    }

    /**
     * Get the user that made the booking
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the quote for this booking
     */
    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the package for this booking
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Get the service for this booking
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the supplier for this booking
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Activity logs relation
     */
    public function activityLogs()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'subject')->latest();
    }

    /**
     * Scope for pending bookings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for confirmed bookings
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }
}
