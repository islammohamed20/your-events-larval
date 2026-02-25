<?php

namespace App\Observers;

use App\Models\AdminNotification;
use App\Models\Order;

class OrderObserver
{
    public function created(Order $order)
    {
        AdminNotification::createIfEnabled('orders', [
            'title' => 'طلب جديد',
            'message' => "طلب جديد #{$order->id} - ".($order->service->name ?? 'خدمة'),
            'icon' => 'fas fa-shopping-cart',
            'color' => 'primary',
            'link' => route('admin.orders.show', $order->id),
            'related_id' => $order->id,
            'related_type' => Order::class,
        ]);
    }
}
