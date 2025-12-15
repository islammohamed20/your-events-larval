<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quote_number',
        'status',
        'payment_status',
        'payment_date',
        'payment_method',
        'payment_reference',
        'payment_notes',
        'subtotal',
        'tax',
        'discount',
        'total',
        'customer_notes',
        'admin_notes',
        'approved_at',
        'rejected_at',
        'accepted_by_supplier_id',
        'supplier_accepted_at',
        'supplier_notes',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'supplier_accepted_at' => 'datetime',
        'payment_date' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Generate unique quote number
     */
    public static function generateQuoteNumber()
    {
        $year = date('Y');
        $lastQuote = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastQuote ? intval(substr($lastQuote->quote_number, -5)) + 1 : 1;
        
        return 'QT-' . $year . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate totals
     */
    public function calculateTotals()
    {
        $itemsSum = $this->items->sum('subtotal');
        $this->subtotal = sprintf('%.2f', $itemsSum);
        $tax = $itemsSum * 0.15;
        $this->tax = sprintf('%.2f', $tax); // 15% ضريبة
        $total = $itemsSum + $tax - ($this->discount ?? 0);
        $this->total = sprintf('%.2f', $total);
        $this->save();
    }

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function acceptedBySupplier()
    {
        return $this->belongsTo(Supplier::class, 'accepted_by_supplier_id');
    }

    /**
     * Activity logs relation
     */
    public function activityLogs()
    {
        return $this->morphMany(\App\Models\ActivityLog::class, 'subject')->latest();
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Accessors
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-secondary">قيد الانتظار</span>',
            'under_review' => '<span class="badge bg-warning">قيد المراجعة</span>',
            'approved' => '<span class="badge bg-success">موافق عليه</span>',
            'rejected' => '<span class="badge bg-danger">مرفوض</span>',
            'completed' => '<span class="badge bg-info">مكتمل</span>',
            'paid' => '<span class="badge bg-primary"><i class="fas fa-check-circle"></i> تم الدفع</span>',
        ];

        return $badges[$this->status] ?? $this->status;
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'قيد الانتظار',
            'under_review' => 'قيد المراجعة',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'completed' => 'مكتمل',
            'paid' => 'تم الدفع',
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Convert paid quote to booking with competitive logic
     */
    public function convertToBooking()
    {
        // التحقق من أن عرض السعر مدفوع
        if ($this->payment_status !== 'paid') {
            throw new \Exception('لا يمكن تحويل عرض السعر إلى حجز قبل الدفع');
        }

        // التحقق من عدم وجود حجز مسبق
        if ($this->bookings()->exists()) {
            throw new \Exception('تم تحويل عرض السعر إلى حجز مسبقاً');
        }

        if ($this->items()->count() === 0) {
            throw new \Exception('لا يمكن تحويل عرض بدون خدمات');
        }

        // إنشاء الحجز
        $booking = Booking::create([
            'user_id' => $this->user_id,
            'quote_id' => $this->id,
            'activity_name' => $this->items()->first()->service_name ?? 'فعالية',
            'service_id' => $this->items()->first()->service_id ?? null,
            'client_name' => $this->user->name,
            'client_email' => $this->user->email,
            'client_phone' => $this->user->phone ?? '',
            'event_date' => now()->addDays(30), // تاريخ افتراضي، يمكن تعديله من العميل
            'event_location' => '',
            'guests_count' => 1,
            'special_requests' => $this->customer_notes,
            'total_amount' => $this->total,
            'payment_method' => $this->payment_method,
            'payment_status' => 'paid',
            'payment_notes' => $this->payment_notes,
            'status' => 'awaiting_supplier', // بانتظار قبول المورد
            'expires_at' => now()->addHours(24), // 24 ساعة للموردين للقبول
        ]);

        // إرسال إشعارات للموردين المؤهلين
        $booking->notifyEligibleSuppliers();

        // تحديث حالة عرض السعر
        $this->update(['status' => 'paid']);

        // إرسال إيميل للعميل بتأكيد الدفع
        Mail::to($this->user->email)->send(new \App\Mail\QuotePaymentConfirmationMail($this));

        return $booking;
    }

    /**
     * Model events for logging
     */
    protected static function boot()
    {
        parent::boot();

        static::created(function ($quote) {
            \App\Models\ActivityLog::record($quote, 'created', 'تم إنشاء عرض سعر جديد', [
                'status' => $quote->status,
                'quote_number' => $quote->quote_number,
                'total' => $quote->total,
            ]);
        });

        static::updated(function ($quote) {
            $changes = $quote->getChanges();
            unset($changes['updated_at']);
            if (!empty($changes)) {
                \App\Models\ActivityLog::record($quote, 'updated', 'تم تعديل عرض السعر', [
                    'changes' => $changes,
                ]);
            }
        });
    }
}
