<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quote_number',
        'status',
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
            'booked' => '<span class="badge bg-primary">تم الحجز</span>',
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
            'booked' => 'تم الحجز',
        ];

        return $statuses[$this->status] ?? $this->status;
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
