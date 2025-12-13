<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CompetitiveOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'event_date',
        'event_time',
        'event_location',
        'guests_count',
        'notes',
        'status',
        'expires_at',
        'accepted_by_supplier_id',
        'accepted_at',
        'supplier_notes',
        'notified_suppliers_count',
        'views_count',
    ];

    protected $casts = [
        'event_date' => 'date',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . strtoupper(Str::random(8));
            }
            
            if (empty($order->expires_at)) {
                $order->expires_at = now()->addHours(24);
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'order_services', 'competitive_order_id', 'service_id')
                    ->withPivot('quantity', 'notes')
                    ->withTimestamps();
    }

    public function acceptedBySupplier()
    {
        return $this->belongsTo(Supplier::class, 'accepted_by_supplier_id');
    }

    public function notifications()
    {
        return $this->hasMany(OrderNotification::class);
    }

    public function notifiedSuppliers()
    {
        return $this->belongsToMany(Supplier::class, 'order_notifications', 'competitive_order_id', 'supplier_id')
                    ->withPivot('notified_at', 'viewed_at', 'responded_at', 'response')
                    ->withTimestamps();
    }

    public function isActive()
    {
        return $this->status === 'pending' && $this->expires_at > now();
    }

    public function isExpired()
    {
        return $this->expires_at <= now() || $this->status === 'expired';
    }

    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    public function acceptBySupplier(Supplier $supplier, $notes = null)
    {
        return \DB::transaction(function () use ($supplier, $notes) {
            $order = self::lockForUpdate()->find($this->id);
            
            if ($order->status !== 'pending') {
                return false;
            }
            
            if ($order->expires_at <= now()) {
                $order->status = 'expired';
                $order->save();
                return false;
            }
            
            $order->status = 'accepted';
            $order->accepted_by_supplier_id = $supplier->id;
            $order->accepted_at = now();
            $order->supplier_notes = $notes;
            $order->save();
            
            $order->notifications()
                  ->where('supplier_id', $supplier->id)
                  ->update([
                      'response' => 'accepted',
                      'responded_at' => now(),
                  ]);
            
            $order->notifications()
                  ->where('supplier_id', '!=', $supplier->id)
                  ->where('response', 'pending')
                  ->update([
                      'response' => 'expired',
                      'responded_at' => now(),
                  ]);
            
            return true;
        });
    }

    public function notifyEligibleSuppliers()
    {
        $serviceIds = $this->services()->pluck('services.id');
        
        $suppliers = Supplier::whereHas('services', function($query) use ($serviceIds) {
            $query->whereIn('services.id', $serviceIds);
        })
        ->where('status', 'approved')
        ->get();
        
        $notifiedCount = 0;
        
        foreach ($suppliers as $supplier) {
            OrderNotification::create([
                'competitive_order_id' => $this->id,
                'supplier_id' => $supplier->id,
                'notified_at' => now(),
            ]);
            
            try {
                \Mail::to($supplier->email)->send(new \App\Mail\OrderNotificationMail($this, $supplier));
                $notifiedCount++;
            } catch (\Exception $e) {
                \Log::error('Failed to send order notification: ' . $e->getMessage());
            }
        }
        
        $this->notified_suppliers_count = $notifiedCount;
        $this->save();
        
        return $notifiedCount;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge bg-warning">قيد الانتظار</span>',
            'accepted' => '<span class="badge bg-success">تم القبول</span>',
            'expired' => '<span class="badge bg-secondary">منتهي</span>',
            'cancelled' => '<span class="badge bg-danger">ملغي</span>',
        ];
        
        return $badges[$this->status] ?? '<span class="badge bg-light">غير معروف</span>';
    }

    public function getTimeRemainingAttribute()
    {
        if ($this->isExpired()) {
            return 'منتهي';
        }
        
        return $this->expires_at->diffForHumans();
    }
}
