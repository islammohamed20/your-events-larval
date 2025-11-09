<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EmailTemplatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'تأكيد الحجز',
                'slug' => 'booking-confirmation',
                'subject' => 'تم تأكيد حجزك #{{booking_number}} - Your Events',
                'body' => '<p>عزيزي {{customer_name}},</p><p>تم تأكيد حجزك بنجاح!</p><p>رقم الحجز: {{booking_number}}</p><p>الخدمة: {{service_name}}</p><p>التاريخ: {{booking_date}}</p>',
                'type' => 'booking',
                'description' => 'يُرسل تلقائياً عند تأكيد الحجز',
                'is_active' => true,
            ],
            [
                'name' => 'رسالة ترحيب',
                'slug' => 'welcome',
                'subject' => 'مرحباً بك في Your Events',
                'body' => '<p>عزيزي {{customer_name}},</p><p>مرحباً بك في Your Events!</p>',
                'type' => 'welcome',
                'description' => 'يُرسل عند تسجيل عضو جديد',
                'is_active' => true,
            ],
        ];

        foreach ($templates as $template) {
            \App\Models\EmailTemplate::create($template);
        }
    }
}
