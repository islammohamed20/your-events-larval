<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'supplier_id',
        'notified_at',
        'viewed_at',
        'responded_at',
        'response',
        'rejection_reason',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'viewed_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    /**
     * Get the booking that owns the notification
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the supplier that received the notification
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Mark notification as viewed
     */
    public function markAsViewed()
    {
        if (!$this->viewed_at) {
            $this->update(['viewed_at' => now()]);
            $this->booking->increment('views_count');
        }
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('response', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('response', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('response', 'rejected');
    }

    public function scopeViewed($query)
    {
        return $query->whereNotNull('viewed_at');
    }

    public function scopeUnviewed($query)
    {
        return $query->whereNull('viewed_at');
    }
}
