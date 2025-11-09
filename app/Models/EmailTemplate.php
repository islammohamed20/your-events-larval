<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'variables',
        'type',
        'is_active',
        'description',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Replace variables in template
     */
    public function render(array $data = []): array
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($data as $key => $value) {
            $placeholder = '{{' . $key . '}}';
            $subject = str_replace($placeholder, $value, $subject);
            $body = str_replace($placeholder, $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }

    /**
     * Get default variables for this template
     */
    public function getDefaultVariables(): array
    {
        $defaults = [
            'booking' => [
                'customer_name' => 'اسم العميل',
                'booking_number' => 'رقم الحجز',
                'service_name' => 'اسم الخدمة',
                'booking_date' => 'تاريخ الحجز',
                'booking_time' => 'وقت الحجز',
                'total_amount' => 'المبلغ الإجمالي',
                'company_name' => 'اسم الشركة',
                'company_phone' => 'رقم الهاتف',
                'company_email' => 'البريد الإلكتروني',
            ],
            'welcome' => [
                'customer_name' => 'اسم العميل',
                'customer_email' => 'البريد الإلكتروني',
                'company_name' => 'اسم الشركة',
                'website_url' => 'رابط الموقع',
            ],
            'reset_password' => [
                'customer_name' => 'اسم العميل',
                'reset_link' => 'رابط إعادة التعيين',
                'expiry_time' => 'مدة الصلاحية',
            ],
            'invoice' => [
                'customer_name' => 'اسم العميل',
                'invoice_number' => 'رقم الفاتورة',
                'invoice_date' => 'تاريخ الفاتورة',
                'total_amount' => 'المبلغ الإجمالي',
                'payment_method' => 'طريقة الدفع',
                'invoice_url' => 'رابط الفاتورة',
            ],
        ];

        return $defaults[$this->type] ?? [];
    }
}
