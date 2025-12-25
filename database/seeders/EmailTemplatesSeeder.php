<?php

namespace Database\Seeders;

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
                'slug' => 'booking-confirmation',
                'values' => [
                    'name' => 'تأكيد الحجز',
                    'subject' => 'تم تأكيد حجزك #{{booking_number}} - Your Events',
                    'body' => '<p>عزيزي {{customer_name}},</p><p>تم تأكيد حجزك بنجاح!</p><p>رقم الحجز: {{booking_number}}</p><p>الخدمة: {{service_name}}</p><p>التاريخ: {{booking_date}}</p>',
                    'type' => 'booking',
                    'description' => 'يُرسل تلقائياً عند تأكيد الحجز',
                    'is_active' => true,
                ],
            ],
            [
                'slug' => 'welcome',
                'values' => [
                    'name' => 'رسالة ترحيب',
                    'subject' => 'مرحباً بك في Your Events',
                    'body' => '<p>عزيزي {{customer_name}},</p><p>مرحباً بك في Your Events!</p>',
                    'type' => 'welcome',
                    'description' => 'يُرسل عند تسجيل عضو جديد',
                    'is_active' => true,
                ],
            ],
            [
                'slug' => 'supplier-approval',
                'values' => [
                    'name' => 'قبول المورد',
                    'subject' => 'تمت الموافقة على حسابك كمورد لدى {{company_name}}',
                    'body' => '<p>عزيزي {{supplier_name}},</p><p>يسعدنا إبلاغك بأنه تمت الموافقة على حسابك كمورد لدى {{company_name}} بتاريخ {{approval_date}}.</p><p>يمكنك الآن تسجيل الدخول إلى لوحة المورد والبدء بإدارة خدماتك:</p><p><a href="{{dashboard_url}}" target="_blank">الدخول إلى لوحة المورد</a></p><hr><p>لأي استفسار، تواصل معنا عبر: {{support_email}}</p><p>تحياتنا، فريق {{company_name}}</p>',
                    'type' => 'supplier_approval',
                    'description' => 'يُرسل تلقائياً عند موافقة الإدارة على المورد',
                    'is_active' => true,
                ],
            ],
        ];

        foreach ($templates as $template) {
            \App\Models\EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                $template['values']
            );
        }
    }
}
