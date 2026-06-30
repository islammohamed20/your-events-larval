<?php

namespace Database\Seeders;

use App\Models\MessageTemplate;
use Illuminate\Database\Seeder;

class WhatsAppMessageTemplatesSeeder extends Seeder
{
    public function run(): void
    {
        MessageTemplate::truncate();

        /*
         * Meta / WhatsApp Business API official template format:
         * - Variables use {{1}}, {{2}}, {{3}} … notation
         * - params_schema is an ordered array of human labels for each variable
         * - Copy these content strings directly into Meta's template manager
         */
        $templates = [
            // ── 1. Welcome ──
            [
                'name' => 'welcome_new_customer',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، أهلاً وسهلاً بك في Your Events. يسعدنا خدمتك. كيف يمكننا مساعدتك اليوم؟',
                'params_schema' => ['اسم العميل'],
            ],
            // ── 2. Booking Confirmation ──
            [
                'name' => 'booking_confirmation',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، تم تأكيد حجزك رقم {{2}} بتاريخ {{3}}. إجمالي المبلغ: {{4}} ر.س. شكراً لاختيارك Your Events.',
                'params_schema' => ['اسم العميل', 'رقم الحجز', 'تاريخ الفعالية', 'إجمالي المبلغ'],
            ],
            // ── 3. Quote Details ──
            [
                'name' => 'quote_details',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، عرض السعر رقم {{2}} جاهز. الإجمالي: {{3}} ر.س. للاطلاع على التفاصيل: https://yourevents.sa/quotes/{{2}}. سيتم تأكيد الموافقة من خلال المنصة.',
                'params_schema' => ['اسم العميل', 'رقم عرض السعر', 'إجمالي المبلغ'],
            ],
            // ── 4. Payment Reminder ──
            [
                'name' => 'payment_reminder',
                'type' => 'marketing',
                'content' => 'مرحباً {{1}}، نذكرك بإتمام دفع باقي قيمة حجزك رقم {{2}} ({{3}} ر.س.) لتأكيد الحجز نهائياً. رابط الدفع متاح عند طلبك.',
                'params_schema' => ['اسم العميل', 'رقم الحجز', 'المبلغ المتبقي'],
            ],
            // ── 5. Order / Booking Status Update ──
            [
                'name' => 'order_status_update',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، نود إشعارك بأن حالة حجزك رقم {{2}} قد تم تحديثها إلى: {{3}}. للاستفسار نحن جاهزون.',
                'params_schema' => ['اسم العميل', 'رقم الحجز', 'الحالة الجديدة'],
            ],
            // ── 5.1 Quote Status Update ──
            [
                'name' => 'quote_status_update',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، نود إشعارك بأن حالة عرض السعر رقم {{2}} قد تم تحديثها إلى: {{3}}. للاطلاع على التفاصيل: https://yourevents.sa/quotes/{{2}}',
                'params_schema' => ['اسم العميل', 'رقم عرض السعر', 'الحالة الجديدة'],
            ],
            // ── 6. Request Additional Info ──
            [
                'name' => 'request_additional_info',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، لاستكمال طلبك نحتاج البيانات التالية: تاريخ الفعالية، المدينة، وعدد الضيوف المتوقع.',
                'params_schema' => ['اسم العميل'],
            ],
            // ── 7. Close Conversation ──
            [
                'name' => 'close_conversation',
                'type' => 'utility',
                'content' => 'شكراً لك {{1}}، تم إنجاز طلبك بنجاح. إذا احتجت أي مساعدة مستقبلاً نحن في خدمتك دائماً.',
                'params_schema' => ['اسم العميل'],
            ],
            // ── 8. Identity Verification (OTP) ──
            [
                'name' => 'identity_verification',
                'type' => 'authentication',
                'content' => 'رمز التحقق الخاص بك هو: {{1}}. صالح لمدة 10 دقائق فقط. لا تشاركه مع أحد.',
                'params_schema' => ['رمز التحقق (OTP)'],
            ],
            // ── 9. Event Reminder ──
            [
                'name' => 'event_reminder',
                'type' => 'utility',
                'content' => 'تذكير {{1}}: موعد فعاليتك في حجز رقم {{2}} بتاريخ {{3}}. نتمنى لكم يوماً ممتعاً.',
                'params_schema' => ['اسم العميل', 'رقم الحجز', 'تاريخ الفعالية'],
            ],
            // ── 10. Feedback Request ──
            [
                'name' => 'feedback_request',
                'type' => 'marketing',
                'content' => 'مرحباً {{1}}، نأمل أن تكون تجربتك مع حجز رقم {{2}} ممتازة. نقدّر تقييمك عبر الرابط التالي: https://yourevents.sa/booking/review/{{2}}',
                'params_schema' => ['اسم العميل', 'رقم الحجز'],
            ],
            // ── 11. Booking Cancelled ──
            [
                'name' => 'booking_cancelled',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، نود إشعارك بإلغاء حجزك رقم {{2}}. لمزيد من التفاصيل يرجى التواصل معنا.',
                'params_schema' => ['اسم العميل', 'رقم الحجز'],
            ],
            // ── 12. Refund Processed ──
            [
                'name' => 'refund_processed',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، تم إجراء استرداد مبلغ {{2}} ر.س. لحجز رقم {{3}}. سيصلك التحويل خلال ٣-٥ أيام عمل.',
                'params_schema' => ['اسم العميل', 'المبلغ المسترد', 'رقم الحجز'],
            ],
            // ── 13. New Message Notification ──
            [
                'name' => 'new_message_notification',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، لديك رسالة جديدة بخصوص حجزك رقم {{2}}. يرجى فتح المحادثة للاطلاع على التفاصيل.',
                'params_schema' => ['اسم العميل', 'رقم الحجز'],
            ],
            // ── 14. Quote Approved ──
            [
                'name' => 'quote_approved',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، تمت الموافقة على عرض السعر رقم {{2}}. للتأكيد والدفع: https://yourevents.sa/quotes/{{2}}/complete-booking-payment',
                'params_schema' => ['اسم العميل', 'رقم عرض السعر'],
            ],
            // ── 15. Deposit Received ──
            [
                'name' => 'deposit_received',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، تم استلام عربون قدره {{2}} ر.س. لحجز رقم {{3}}. شكراً لثقتك بنا.',
                'params_schema' => ['اسم العميل', 'مبلغ العربون', 'رقم الحجز'],
            ],
            // ── 16. Quote Payment Confirmed ──
            [
                'name' => 'quote_payment_confirmed',
                'type' => 'utility',
                'content' => 'مرحباً {{1}}، تم تأكيد استلام مبلغ {{2}} ر.س. لعرض السعر رقم {{3}}. شكراً لك، سيتم تأكيد حجزك قريباً.',
                'params_schema' => ['اسم العميل', 'المبلغ المدفوع', 'رقم عرض السعر'],
            ],
        ];

        foreach ($templates as $template) {
            MessageTemplate::updateOrCreate(
                ['name' => $template['name']],
                [
                    'content' => $template['content'],
                    'type' => $template['type'],
                    'params_schema' => $template['params_schema'] ?? null,
                ]
            );
        }
    }
}