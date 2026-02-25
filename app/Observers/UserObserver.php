<?php

namespace App\Observers;

use App\Models\AdminNotification;
use App\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        // إشعار فقط عند تسجيل عميل جديد (ليس مورد)
        if ($user->role === 'customer' || ! $user->role) {
            AdminNotification::createIfEnabled('customers', [
                'title' => 'عميل جديد',
                'message' => "تسجيل عميل جديد: {$user->name}",
                'icon' => 'fas fa-user-plus',
                'color' => 'primary',
                'link' => route('admin.customers.show', $user->id),
                'related_id' => $user->id,
                'related_type' => User::class,
            ]);
        }
    }
}
