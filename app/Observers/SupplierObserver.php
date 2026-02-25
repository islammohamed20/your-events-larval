<?php

namespace App\Observers;

use App\Models\AdminNotification;
use App\Models\Supplier;

class SupplierObserver
{
    public function created(Supplier $supplier)
    {
        AdminNotification::createIfEnabled('suppliers', [
            'title' => 'مورد جديد',
            'message' => "تسجيل مورد جديد: {$supplier->company_name}",
            'icon' => 'fas fa-store',
            'color' => 'info',
            'link' => route('admin.suppliers.show', $supplier->id),
            'related_id' => $supplier->id,
            'related_type' => Supplier::class,
        ]);
    }

    public function updated(Supplier $supplier)
    {
        // إشعار عند تفعيل المورد
        if ($supplier->isDirty('is_active')) {
            if ($supplier->is_active) {
                AdminNotification::createIfEnabled('suppliers', [
                    'title' => 'تم تفعيل مورد',
                    'message' => "تم تفعيل المورد: {$supplier->company_name}",
                    'icon' => 'fas fa-check-circle',
                    'color' => 'success',
                    'link' => route('admin.suppliers.show', $supplier->id),
                    'related_id' => $supplier->id,
                    'related_type' => Supplier::class,
                ]);
            }
        }
    }
}
