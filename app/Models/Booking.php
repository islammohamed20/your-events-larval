<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        'event_lat',
        'event_lng',
        'guests_count',
        'special_requests',
        'total_amount',
        'payment_method',
        'payment_status',
        'payment_notes',
        'status',
        'booking_reference',
        'expires_at',
        'notified_suppliers_count',
        'views_count',
        'accepted_at',
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_lat' => 'float',
        'event_lng' => 'float',
        'total_amount' => 'decimal:2',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * Generate booking reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            $booking->booking_reference = 'YE-'.now()->format('Y-m-d-H-i-s').'-'.substr((string) microtime(true), -3);
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
            if (! empty($changes)) {
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
     * Get all notifications sent for this booking
     */
    public function notifications()
    {
        return $this->hasMany(BookingNotification::class);
    }

    /**
     * Get eligible suppliers for this booking services
     */
    public function getEligibleSuppliers()
    {
        // جمع جميع الخدمات من quote items
        if (! $this->quote_id) {
            return collect();
        }

        $serviceIds = $this->quote->items()->pluck('service_id')->unique();

        // البحث عن الموردين الذين قاموا بتسجيل هذه الخدمات
        return Supplier::whereHas('services', function ($query) use ($serviceIds) {
            $query->whereIn('services.id', $serviceIds);
        })
            ->where('status', 'approved')
            ->whereNotNull('email_verified_at')
            ->get();
    }

    /**
     * Send notifications to all eligible suppliers
     */
    public function notifyEligibleSuppliers()
    {
        $suppliers = $this->getEligibleSuppliers();

        foreach ($suppliers as $supplier) {
            if (! $supplier instanceof Supplier) {
                continue;
            }

            // إنشاء الإشعار
            BookingNotification::create([
                'booking_id' => $this->id,
                'supplier_id' => $supplier->id,
                'notified_at' => now(),
            ]);

            // إرسال البريد الإلكتروني
            try {
                Mail::to($supplier->email)->send(new \App\Mail\BookingNotificationMail($this, $supplier));
            } catch (\Exception $e) {
                Log::error('Failed to send booking notification email to supplier: '.$supplier->email.' - '.$e->getMessage());
            }
        }

        // تحديث عدد الموردين المُشعرين
        $this->update(['notified_suppliers_count' => $suppliers->count()]);
    }

    /**
     * Accept booking by supplier (First-Come-First-Served)
     */
    public function acceptBySupplier(Supplier $supplier, $notes = null)
    {
        return DB::transaction(function () use ($supplier) {
            // قفل الحجز للتحديث لتجنب race conditions
            $booking = self::where('id', $this->id)->lockForUpdate()->first();

            // التحقق من أن الحجز لم ينتهي
            if ($booking->isExpired()) {
                throw new \Exception('انتهت مهلة قبول هذا الحجز');
            }

            // التحقق من أن الحجز لم يتم قبوله مسبقاً
            if ($booking->supplier_id) {
                throw new \Exception('تم قبول هذا الحجز من قبل مورد آخر');
            }

            // قبول الحجز
            $booking->update([
                'supplier_id' => $supplier->id,
                'status' => 'confirmed',
                'accepted_at' => now(),
            ]);

            // تحديث إشعار المورد الحالي
            $notification = BookingNotification::where('booking_id', $booking->id)
                ->where('supplier_id', $supplier->id)
                ->first();

            if ($notification) {
                $notification->update([
                    'response' => 'accepted',
                    'responded_at' => now(),
                ]);
            }

            // تحديث باقي الإشعارات كـ expired
            BookingNotification::where('booking_id', $booking->id)
                ->where('supplier_id', '!=', $supplier->id)
                ->where('response', 'pending')
                ->update([
                    'response' => 'expired',
                    'responded_at' => now(),
                ]);

            // إرسال بريد للعميل بتأكيد قبول الحجز
            try {
                Mail::to($booking->user->email)->send(new \App\Mail\BookingAcceptedBySupplierMail($booking));
            } catch (\Exception $e) {
                Log::error('Failed to send booking accepted email: '.$e->getMessage());
            }

            // تسجيل في activity log
            ActivityLog::record($booking, 'supplier_accepted', 'تم قبول الحجز من قبل المورد', [
                'supplier_id' => $supplier->id,
                'supplier_name' => $supplier->company_name,
            ]);

            return $booking->fresh();
        });
    }

    /**
     * Reject booking by supplier
     */
    public function rejectBySupplier(Supplier $supplier, $reason = null)
    {
        $notification = BookingNotification::where('booking_id', $this->id)
            ->where('supplier_id', $supplier->id)
            ->first();

        if ($notification) {
            $notification->update([
                'response' => 'rejected',
                'responded_at' => now(),
                'rejection_reason' => $reason,
            ]);
        }

        ActivityLog::record($this, 'supplier_rejected', 'رفض المورد الحجز', [
            'supplier_id' => $supplier->id,
            'supplier_name' => $supplier->company_name,
            'reason' => $reason,
        ]);
    }

    /**
     * Check if booking is expired
     */
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    /**
     * Check if booking is still active for suppliers
     */
    public function isActive()
    {
        return $this->status === 'awaiting_supplier' && ! $this->isExpired();
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning"><i class="fas fa-clock"></i> في الانتظار</span>',
            'awaiting_supplier' => '<span class="badge bg-info"><i class="fas fa-hourglass-half"></i> بانتظار المورد</span>',
            'confirmed' => '<span class="badge bg-success"><i class="fas fa-check"></i> مؤكد</span>',
            'cancelled' => '<span class="badge bg-danger"><i class="fas fa-times"></i> ملغي</span>',
            'expired' => '<span class="badge bg-secondary"><i class="fas fa-clock"></i> منتهي</span>',
            'completed' => '<span class="badge bg-primary"><i class="fas fa-check-double"></i> مكتمل</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'في الانتظار',
            'awaiting_supplier' => 'بانتظار قبول المورد',
            'confirmed' => 'مؤكد',
            'cancelled' => 'ملغي',
            'expired' => 'منتهي الصلاحية',
            'completed' => 'مكتمل',
        ];

        return $statuses[$this->status] ?? $this->status;
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

    /**
     * Scope bookings that block service-day availability
     */
    public function scopeBlockingServiceDate($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('status')
                ->orWhereNotIn('status', ['cancelled', 'expired']);
        });
    }

    /**
     * Check if a service is already booked in a specific date.
     */
    public static function isServiceDateUnavailable(int $serviceId, string $eventDate): bool
    {
        return static::query()
            ->where('service_id', $serviceId)
            ->whereDate('event_date', $eventDate)
            ->blockingServiceDate()
            ->exists();
    }
}
