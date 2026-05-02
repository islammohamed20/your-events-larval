<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class WhatsAppMessageTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        // Delete all old Arabic templates before creating new English ones
        MessageTemplate::truncate();

        $templates = [
            [
                'name' => 'welcome_new_customer',
                'type' => 'utility',
                'content' => 'مرحباً بك في Your Events. يسعدنا خدمتك. كيف يمكننا مساعدتك اليوم؟',
            ],
            [
                'name' => 'request_order_number',
                'type' => 'utility',
                'content' => 'لخدمتك بشكل أسرع، فضلاً أرسل رقم الحجز أو رقم عرض السعر.',
            ],
            [
                'name' => 'order_confirmation',
                'type' => 'utility',
                'content' => 'تم استلام طلبك بنجاح، وجارٍ مراجعته من الفريق المختص. سنوافيك بالتحديث قريباً.',
            ],
            [
                'name' => 'order_status_update',
                'type' => 'utility',
                'content' => 'نود إشعارك بأنه تم تحديث حالة طلبك. إذا رغبت بالتفاصيل يمكننا إرسالها فوراً.',
            ],
            [
                'name' => 'request_additional_info',
                'type' => 'utility',
                'content' => 'لاستكمال الطلب نحتاج البيانات التالية: تاريخ الفعالية، المدينة، وعدد الضيوف المتوقع.',
            ],
            [
                'name' => 'payment_reminder',
                'type' => 'marketing',
                'content' => 'لإكمال تأكيد الحجز، يرجى إتمام عملية الدفع من الرابط المرسل لك. في حال واجهتك مشكلة نحن جاهزون للمساعدة.',
            ],
            [
                'name' => 'close_conversation',
                'type' => 'utility',
                'content' => 'تم تنفيذ طلبك بنجاح. إذا احتجت أي مساعدة مستقبلًا نحن في خدمتك دائمًا.',
            ],
            [
                'name' => 'identity_verification',
                'type' => 'authentication',
                'content' => 'رمز التحقق الخاص بك هو: {{otp}}. صالح لمدة 10 دقائق فقط.',
            ],
        ];

        foreach ($templates as $template) {
            MessageTemplate::updateOrCreate(
                ['name' => $template['name']],
                [
                    'content' => $template['content'],
                    'type' => $template['type'],
                ]
            );
        }
    }
}