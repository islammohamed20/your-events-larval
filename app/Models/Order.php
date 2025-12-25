<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'supplier_id',
        'service_id',
        'category_id',
        'quantity',
        'price',
        'customer_notes',
        'general_notes',
        'status',
        'assigned_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * العلاقات
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // الموردين الذين تم إرسال الطلب لهم
    public function supplierStatuses()
    {
        return $this->hasMany(SupplierOrderStatus::class);
    }

    /**
     * Helper Methods
     */

    // التحقق من أن الطلب لا يزال متاح للقبول
    public function isAvailable(): bool
    {
        return $this->status === 'pending';
    }

    // قبول الطلب من قبل مورد
    public function acceptBySupplier(int $supplierId): bool
    {
        // تحقق من أن الطلب ما زال متاح
        if (! $this->isAvailable()) {
            return false;
        }

        // تحديث حالة الطلب
        $this->update([
            'supplier_id' => $supplierId,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        // تحديث حالة المورد
        SupplierOrderStatus::where('order_id', $this->id)
            ->where('supplier_id', $supplierId)
            ->update(['status' => 'accepted', 'accepted_at' => now()]);

        return true;
    }
}
