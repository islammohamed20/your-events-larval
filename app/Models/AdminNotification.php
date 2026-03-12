<?php

namespace App\Models;

use App\Services\AdminNotificationEmailService;
use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected static function booted()
    {
        static::created(function (AdminNotification $notification) {
            app(AdminNotificationEmailService::class)->sendForNotification($notification);
        });
    }

    protected $fillable = [
        'type',
        'title',
        'message',
        'icon',
        'color',
        'link',
        'related_id',
        'related_type',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Scopes
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRecent($query, $minutes = 5)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }

    // Methods
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    // Helper method to check if notifications are enabled for a type
    public static function isNotificationEnabled($type)
    {
        $settings = Setting::getSettings([
            'notifications_enabled',
            "notification_{$type}"
        ]);
        
        // Check if notifications are globally enabled and this type is enabled
        return ($settings['notifications_enabled'] ?? true) && ($settings["notification_{$type}"] ?? true);
    }

    // Enhanced create method that checks settings
    public static function createIfEnabled($type, $data)
    {
        if (!self::isNotificationEnabled($type)) {
            return null;
        }
        
        return self::create(array_merge($data, ['type' => $type]));
    }

    // Static methods to create notifications
    public static function notifyNewQuote($quote)
    {
        return self::createIfEnabled('quotes', [
            'type' => 'quote',
            'title' => 'عرض سعر جديد',
            'message' => "عرض سعر جديد #{$quote->quote_number} من {$quote->user->name}",
            'icon' => 'fas fa-file-invoice-dollar',
            'color' => 'info',
            'link' => route('admin.quotes.show', $quote->id),
            'related_id' => $quote->id,
            'related_type' => Quote::class,
        ]);
    }

    public static function notifyNewBooking($booking)
    {
        return self::create([
            'type' => 'booking',
            'title' => 'حجز جديد',
            'message' => "حجز جديد #{$booking->id} - {$booking->activity_name}",
            'icon' => 'fas fa-calendar-check',
            'color' => 'success',
            'link' => route('admin.bookings.show', $booking->id),
            'related_id' => $booking->id,
            'related_type' => Booking::class,
        ]);
    }

    public static function notifyNewOrder($order)
    {
        return self::create([
            'type' => 'order',
            'title' => 'طلب جديد',
            'message' => "طلب جديد #{$order->id} - {$order->service->name}",
            'icon' => 'fas fa-shopping-cart',
            'color' => 'primary',
            'link' => route('admin.orders.show', $order->id),
            'related_id' => $order->id,
            'related_type' => Order::class,
        ]);
    }

    public static function notifyNewPayment($payment)
    {
        return self::create([
            'type' => 'payment',
            'title' => 'دفعة جديدة',
            'message' => "دفعة جديدة بقيمة {$payment->amount} ر.س",
            'icon' => 'fas fa-credit-card',
            'color' => 'success',
            'link' => route('admin.payments.show', $payment->id),
            'related_id' => $payment->id,
            'related_type' => Payment::class,
        ]);
    }

    public static function notifyNewContact($contact)
    {
        return self::create([
            'type' => 'contact',
            'title' => 'رسالة تواصل جديدة',
            'message' => "رسالة من {$contact->name}: {$contact->subject}",
            'icon' => 'fas fa-envelope',
            'color' => 'warning',
            'link' => route('admin.contact-messages.show', $contact->id),
            'related_id' => $contact->id,
            'related_type' => ContactMessage::class,
        ]);
    }

    public static function notifyNewSupplier($supplier)
    {
        return self::create([
            'type' => 'supplier',
            'title' => 'مورد جديد',
            'message' => "تسجيل مورد جديد: {$supplier->name}",
            'icon' => 'fas fa-truck',
            'color' => 'info',
            'link' => route('admin.suppliers.show', $supplier->id),
            'related_id' => $supplier->id,
            'related_type' => Supplier::class,
        ]);
    }

    public static function notifyNewCustomer($customer)
    {
        return self::create([
            'type' => 'customer',
            'title' => 'عميل جديد',
            'message' => "تسجيل عميل جديد: {$customer->name}",
            'icon' => 'fas fa-user-plus',
            'color' => 'primary',
            'link' => route('admin.customers.show', $customer->id),
            'related_id' => $customer->id,
            'related_type' => User::class,
        ]);
    }

    public static function notifyQuoteStatusChange($quote, $oldStatus)
    {
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليه',
            'rejected' => 'مرفوض',
            'completed' => 'مكتمل',
            'paid' => 'تم الدفع',
        ];

        return self::create([
            'type' => 'quote',
            'title' => 'تحديث حالة عرض السعر',
            'message' => "عرض السعر #{$quote->quote_number} تغير إلى: ".($statusLabels[$quote->status] ?? $quote->status),
            'icon' => 'fas fa-sync-alt',
            'color' => $quote->status === 'approved' ? 'success' : ($quote->status === 'rejected' ? 'danger' : 'info'),
            'link' => route('admin.quotes.show', $quote->id),
            'related_id' => $quote->id,
            'related_type' => Quote::class,
        ]);
    }
}
