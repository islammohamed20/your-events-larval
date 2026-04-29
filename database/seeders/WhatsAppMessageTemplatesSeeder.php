<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class WhatsAppMessageTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'name' => 'ترحيب عميل جديد',
                'type' => 'utility',
                'content' => 'مرحباً بك في Your Events. يسعدنا خدمتك. كيف يمكننا مساعدتك اليوم؟',
            ],
            [
                'name' => 'طلب رقم الطلب',
                'type' => 'utility',
                'content' => 'لخدمتك بشكل أسرع، فضلاً أرسل رقم الحجز أو رقم عرض السعر.',
            ],
            [
                'name' => 'تأكيد استلام الطلب',
                'type' => 'utility',
                'content' => 'تم استلام طلبك بنجاح، وجارٍ مراجعته من الفريق المختص. سنوافيك بالتحديث قريباً.',
            ],
            [
                'name' => 'تحديث حالة الطلب',
                'type' => 'utility',
                'content' => 'نود إشعارك بأنه تم تحديث حالة طلبك. إذا رغبت بالتفاصيل يمكننا إرسالها فوراً.',
            ],
            [
                'name' => 'طلب بيانات إضافية',
                'type' => 'utility',
                'content' => 'لاستكمال الطلب نحتاج البيانات التالية: تاريخ الفعالية، المدينة، وعدد الضيوف المتوقع.',
            ],
            [
                'name' => 'تذكير إتمام الدفع',
                'type' => 'marketing',
                'content' => 'لإكمال تأكيد الحجز، يرجى إتمام عملية الدفع من الرابط المرسل لك. في حال واجهتك مشكلة نحن جاهزون للمساعدة.',
            ],
            [
                'name' => 'إغلاق المحادثة',
                'type' => 'utility',
                'content' => 'تم تنفيذ طلبك بنجاح. إذا احتجت أي مساعدة مستقبلًا نحن في خدمتك دائمًا.',
            ],
            [
                'name' => 'التحقق من الهوية',
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