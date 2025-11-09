# 🔔 دليل دمج n8n مع Laravel - إشعارات عروض الأسعار
## n8n Integration for Quote Notifications

تاريخ: 12 أكتوبر 2025

---

## 📋 المتطلبات

السيناريو المطلوب:
```
عند إنشاء عرض سعر جديد من عميل
    ↓
Laravel يرسل بيانات إلى n8n
    ↓
n8n يرسل:
  1. ✉️ إيميل Gmail للإدارة
  2. 📱 رسالة واتساب للإدارة
```

---

## 🚀 الخطوات الكاملة

### المرحلة 1️⃣: إعداد n8n Workflow

#### الخطوة 1: الوصول إلى n8n
```bash
# افتح المتصفح واذهب إلى:
http://localhost:5678
# أو
http://72.61.154.100:5678
```

#### الخطوة 2: إنشاء Workflow جديد
```
1. اضغط "New workflow"
2. اسم الـ workflow: "Laravel Quote Notifications"
3. احفظ
```

---

### المرحلة 2️⃣: إضافة Webhook Node

#### الخطوة 1: إضافة Webhook
```
1. اضغط "+" لإضافة node
2. ابحث عن "Webhook"
3. اختر "Webhook"
4. إعدادات الـ Webhook:
   - HTTP Method: POST
   - Path: quote-created
   - Response Mode: "Immediately"
```

#### الخطوة 2: نسخ Webhook URL
بعد حفظ الـ Webhook، ستحصل على URL مثل:
```
http://localhost:5678/webhook/quote-created
أو
http://72.61.154.100:5678/webhook/quote-created
```

**📝 احفظ هذا الرابط - ستحتاجه في Laravel**

#### الخطوة 3: اختبار الـ Webhook
```json
// Test Data لاختبار الـ webhook
{
  "quote_id": 1,
  "quote_number": "QT-2024-001",
  "customer_name": "عبدالله محمد",
  "customer_email": "customer@example.com",
  "customer_phone": "0501234567",
  "total": 5000,
  "items_count": 3,
  "customer_notes": "احتاج التنسيق يكون فخم",
  "created_at": "2025-10-12 10:30:00",
  "quote_url": "http://72.61.154.100/admin/quotes/1"
}
```

---

### المرحلة 3️⃣: إضافة Gmail Node

#### الخطوة 1: إضافة Gmail Node
```
1. اضغط "+" بعد الـ Webhook node
2. ابحث عن "Gmail"
3. اختر "Gmail"
4. Operation: "Send Email"
```

#### الخطوة 2: ربط حساب Gmail
```
1. اضغط "Create New Credentials"
2. اختر "OAuth2 API"
3. ستحتاج:
   - Client ID من Google Console
   - Client Secret من Google Console
```

##### كيفية الحصول على Gmail API Credentials:

**أ) اذهب إلى Google Cloud Console:**
```
https://console.cloud.google.com/
```

**ب) إنشاء مشروع جديد:**
```
1. اضغط "Select a project"
2. اضغط "New Project"
3. اسم المشروع: "Your Events Notifications"
4. Create
```

**ج) تفعيل Gmail API:**
```
1. اذهب إلى "APIs & Services" → "Library"
2. ابحث عن "Gmail API"
3. اضغط "Enable"
```

**د) إنشاء OAuth Credentials:**
```
1. اذهب إلى "APIs & Services" → "Credentials"
2. اضغط "Create Credentials" → "OAuth client ID"
3. Application type: "Web application"
4. Name: "n8n Gmail"
5. Authorized redirect URIs:
   - http://localhost:5678/rest/oauth2-credential/callback
   - http://72.61.154.100:5678/rest/oauth2-credential/callback
6. Create
7. احفظ:
   - Client ID
   - Client Secret
```

**ه) أدخل Credentials في n8n:**
```
1. في n8n Gmail node
2. Credentials → Create New
3. ألصق Client ID و Client Secret
4. Connect My Account
5. سجل دخول بحساب Gmail
6. وافق على الصلاحيات
```

#### الخطوة 3: إعداد محتوى الإيميل

**في Gmail Node، أدخل:**

```javascript
// To Email (البريد المستقبل):
admin@your-events.com

// Subject (الموضوع):
🔔 عرض سعر جديد من {{ $json.customer_name }} - {{ $json.quote_number }}

// Body (المحتوى):
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: white; border-radius: 10px; padding: 30px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #ef4870 0%, #ff7ba3 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0; margin: -30px -30px 20px -30px; }
        .badge { background-color: #ffc107; color: #000; padding: 5px 15px; border-radius: 20px; font-size: 14px; display: inline-block; }
        .info-box { background-color: #f8f9fa; border-right: 4px solid #ef4870; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .button { background: linear-gradient(135deg, #ef4870 0%, #ff7ba3 100%); color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; display: inline-block; margin-top: 20px; }
        .footer { text-align: center; margin-top: 30px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">🎉 عرض سعر جديد!</h1>
            <p style="margin: 5px 0 0 0;">تم استلام طلب عرض سعر من عميل جديد</p>
        </div>

        <div style="padding: 20px;">
            <h2>معلومات العرض</h2>
            <div class="info-box">
                <p><strong>📋 رقم العرض:</strong> {{ $json.quote_number }}</p>
                <p><strong>👤 اسم العميل:</strong> {{ $json.customer_name }}</p>
                <p><strong>📧 البريد الإلكتروني:</strong> {{ $json.customer_email }}</p>
                <p><strong>📱 رقم الجوال:</strong> {{ $json.customer_phone }}</p>
                <p><strong>💰 المبلغ الإجمالي:</strong> {{ $json.total }} ريال</p>
                <p><strong>📦 عدد الخدمات:</strong> {{ $json.items_count }} خدمة</p>
                <p><strong>🕐 التاريخ والوقت:</strong> {{ $json.created_at }}</p>
            </div>

            <h3>ملاحظات العميل:</h3>
            <div class="info-box">
                <p>{{ $json.customer_notes || 'لا توجد ملاحظات' }}</p>
            </div>

            <div style="text-align: center;">
                <a href="{{ $json.quote_url }}" class="button">
                    عرض التفاصيل الكاملة →
                </a>
            </div>
        </div>

        <div class="footer">
            <p>هذه رسالة تلقائية من نظام Your Events</p>
            <p>© 2025 Your Events - جميع الحقوق محفوظة</p>
        </div>
    </div>
</body>
</html>

// Email Format: HTML
```

---

### المرحلة 4️⃣: إضافة WhatsApp Node

لإرسال رسائل واتساب، لديك 3 خيارات:

#### الخيار 1: Twilio (الأسهل والأسرع) ⭐ مُوصى به

**أ) إنشاء حساب Twilio:**
```
1. اذهب إلى: https://www.twilio.com/
2. Sign Up for Free
3. احصل على:
   - Account SID
   - Auth Token
   - WhatsApp Sandbox Number
```

**ب) إعداد WhatsApp Sandbox:**
```
1. في Twilio Console → Messaging → Try it out → Send a WhatsApp message
2. اتبع التعليمات لربط رقم الواتساب للاختبار
3. أرسل الكود المطلوب لرقم Twilio
```

**ج) إضافة HTTP Request Node في n8n:**
```
1. بعد Gmail node، اضغط "+"
2. اختر "HTTP Request"
3. Method: POST
4. URL: https://api.twilio.com/2010-04-01/Accounts/YOUR_ACCOUNT_SID/Messages.json
5. Authentication: Basic Auth
   - User: YOUR_ACCOUNT_SID
   - Password: YOUR_AUTH_TOKEN
6. Body Content Type: Form-Data
7. Body Parameters:
```

```json
{
  "From": "whatsapp:+14155238886",
  "To": "whatsapp:+966501234567",
  "Body": "🔔 *عرض سعر جديد!*\n\n*رقم العرض:* {{ $json.quote_number }}\n*العميل:* {{ $json.customer_name }}\n*الجوال:* {{ $json.customer_phone }}\n*المبلغ:* {{ $json.total }} ريال\n*عدد الخدمات:* {{ $json.items_count }}\n\n*ملاحظات العميل:*\n{{ $json.customer_notes || 'لا توجد' }}\n\n🔗 رابط العرض:\n{{ $json.quote_url }}"
}
```

#### الخيار 2: WhatsApp Business API (للإنتاج)

**يتطلب:**
- حساب WhatsApp Business معتمد
- رقم هاتف تجاري
- عملية موافقة من Meta

**الخطوات:**
```
1. اذهب إلى: https://business.whatsapp.com/
2. سجل كـ Business
3. اطلب API access
4. بعد الموافقة، استخدم API credentials في n8n
```

#### الخيار 3: واتساب عبر Make.com / Zapier (بديل)

```
يمكنك استخدام منصات No-code أخرى
لكن Twilio هو الأسرع والأسهل
```

---

### المرحلة 5️⃣: تفعيل Workflow

#### الخطوة 1: اختبار الـ Workflow
```
1. اضغط "Execute Workflow" في n8n
2. أرسل test data للـ webhook باستخدام Postman أو cURL:
```

```bash
curl -X POST http://localhost:5678/webhook/quote-created \
  -H "Content-Type: application/json" \
  -d '{
    "quote_id": 1,
    "quote_number": "QT-2024-001",
    "customer_name": "عبدالله محمد",
    "customer_email": "customer@example.com",
    "customer_phone": "0501234567",
    "total": 5000,
    "items_count": 3,
    "customer_notes": "احتاج التنسيق يكون فخم",
    "created_at": "2025-10-12 10:30:00",
    "quote_url": "http://72.61.154.100/admin/quotes/1"
  }'
```

#### الخطوة 2: تفعيل الـ Workflow
```
1. إذا كان الاختبار ناجح ✅
2. اضغط "Active" في أعلى الـ workflow
3. الآن الـ webhook يعمل بشكل دائم
```

---

### المرحلة 6️⃣: دمج Laravel مع n8n

#### الخطوة 1: إنشاء Service Class للإشعارات

**إنشاء ملف:** `app/Services/N8nNotificationService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nNotificationService
{
    protected $webhookUrl;

    public function __construct()
    {
        $this->webhookUrl = config('services.n8n.webhook_url');
    }

    /**
     * إرسال إشعار عرض سعر جديد
     */
    public function sendNewQuoteNotification($quote)
    {
        try {
            $data = [
                'quote_id' => $quote->id,
                'quote_number' => $quote->quote_number,
                'customer_name' => $quote->user->name,
                'customer_email' => $quote->user->email,
                'customer_phone' => $quote->user->phone ?? 'غير متوفر',
                'total' => number_format($quote->total, 2),
                'items_count' => $quote->items->count(),
                'customer_notes' => $quote->customer_notes ?? 'لا توجد ملاحظات',
                'created_at' => $quote->created_at->format('Y-m-d H:i:s'),
                'quote_url' => url('/admin/quotes/' . $quote->id),
            ];

            $response = Http::timeout(10)->post($this->webhookUrl, $data);

            if ($response->successful()) {
                Log::info('n8n notification sent successfully for quote: ' . $quote->id);
                return true;
            } else {
                Log::error('n8n notification failed for quote: ' . $quote->id, [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('n8n notification exception for quote: ' . $quote->id, [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
```

#### الخطوة 2: إضافة Configuration

**إضافة في:** `config/services.php`

```php
return [
    // ... existing services

    'n8n' => [
        'webhook_url' => env('N8N_WEBHOOK_URL', 'http://localhost:5678/webhook/quote-created'),
    ],
];
```

#### الخطوة 3: إضافة في `.env`

```env
# n8n Configuration
N8N_WEBHOOK_URL=http://localhost:5678/webhook/quote-created
# أو استخدم IP السيرفر:
# N8N_WEBHOOK_URL=http://127.0.0.1:5678/webhook/quote-created
```

#### الخطوة 4: تعديل QuoteController

**في:** `app/Http/Controllers/QuoteController.php`

```php
use App\Services\N8nNotificationService;

class QuoteController extends Controller
{
    protected $n8nService;

    public function __construct(N8nNotificationService $n8nService)
    {
        $this->middleware('auth');
        $this->n8nService = $n8nService;
    }

    public function checkout(Request $request)
    {
        // ... existing code ...

        // Create quote
        $quote = Quote::create([
            'user_id' => auth()->id(),
            'quote_number' => Quote::generateQuoteNumber(),
            'status' => 'pending',
            'customer_notes' => $validated['customer_notes'] ?? null,
        ]);

        // Create quote items
        foreach ($cartItems as $cartItem) {
            // ... existing code ...
        }

        // Calculate totals
        $quote->calculateTotals();

        // Clear cart
        CartItem::clearCart();

        // ⭐ إرسال إشعار n8n
        $this->n8nService->sendNewQuoteNotification($quote->fresh(['items', 'user']));

        return redirect()->route('quotes.show', $quote)
            ->with('success', 'تم إنشاء عرض السعر بنجاح! سيتم مراجعته قريباً.');
    }
}
```

#### الخطوة 5: إنشاء Event & Listener (اختياري - أفضل)

**إنشاء Event:**
```bash
php artisan make:event QuoteCreated
```

**في:** `app/Events/QuoteCreated.php`
```php
<?php

namespace App\Events;

use App\Models\Quote;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuoteCreated
{
    use Dispatchable, SerializesModels;

    public $quote;

    public function __construct(Quote $quote)
    {
        $this->quote = $quote;
    }
}
```

**إنشاء Listener:**
```bash
php artisan make:listener SendQuoteNotification
```

**في:** `app/Listeners/SendQuoteNotification.php`
```php
<?php

namespace App\Listeners;

use App\Events\QuoteCreated;
use App\Services\N8nNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendQuoteNotification implements ShouldQueue
{
    protected $n8nService;

    public function __construct(N8nNotificationService $n8nService)
    {
        $this->n8nService = $n8nService;
    }

    public function handle(QuoteCreated $event)
    {
        $this->n8nService->sendNewQuoteNotification($event->quote);
    }
}
```

**تسجيل في:** `app/Providers/EventServiceProvider.php`
```php
use App\Events\QuoteCreated;
use App\Listeners\SendQuoteNotification;

protected $listen = [
    QuoteCreated::class => [
        SendQuoteNotification::class,
    ],
];
```

**في QuoteController:**
```php
use App\Events\QuoteCreated;

public function checkout(Request $request)
{
    // ... create quote ...
    
    // Fire event
    event(new QuoteCreated($quote->fresh(['items', 'user'])));
    
    return redirect()->route('quotes.show', $quote)->with('success', '...');
}
```

---

## 🧪 الاختبار

### 1. اختبار n8n Workflow
```bash
# Test webhook مباشرة
curl -X POST http://localhost:5678/webhook/quote-created \
  -H "Content-Type: application/json" \
  -d '{"quote_number":"TEST-001","customer_name":"Test User"}'
```

### 2. اختبار من Laravel
```bash
# في Laravel Tinker
php artisan tinker

# أنشئ quote للاختبار
$quote = Quote::with(['items', 'user'])->first();
app(\App\Services\N8nNotificationService::class)->sendNewQuoteNotification($quote);
```

### 3. اختبار كامل
```
1. سجل دخول كعميل
2. أضف خدمات للسلة
3. اضغط Checkout
4. انتظر:
   ✅ إيميل Gmail
   ✅ رسالة واتساب
```

---

## 📊 المخطط التوضيحي

```
عميل يطلب عرض سعر
         ↓
Laravel QuoteController::checkout()
         ↓
إنشاء Quote في Database
         ↓
N8nNotificationService::sendNewQuoteNotification()
         ↓
HTTP POST → n8n Webhook
         ↓
    ┌────n8n Workflow────┐
    ↓                    ↓
Gmail Node        WhatsApp Node
    ↓                    ↓
إيميل للإدارة    رسالة واتساب للإدارة
```

---

## 🔧 Troubleshooting

### مشكلة: n8n لا يستقبل البيانات
```bash
# تحقق من أن n8n يعمل
curl http://localhost:5678/healthz

# تحقق من الـ webhook URL في .env
php artisan config:clear
php artisan config:cache
```

### مشكلة: Gmail لا يرسل
```
1. تأكد من OAuth credentials صحيحة
2. تأكد من Gmail API مُفعّل في Google Console
3. جرب Disconnect & Reconnect في n8n
```

### مشكلة: واتساب لا يرسل
```
1. تأكد من Twilio credentials
2. تأكد من رقم الواتساب مُفعّل في Sandbox
3. تحقق من صيغة الرقم: whatsapp:+966XXXXXXXXX
```

---

## 🔐 الأمان

### تأمين الـ Webhook
```javascript
// في n8n Webhook node → Headers
// أضف Authentication header
{
  "Authorization": "Bearer YOUR_SECRET_TOKEN"
}
```

**في Laravel:**
```php
$response = Http::withHeaders([
    'Authorization' => 'Bearer ' . env('N8N_WEBHOOK_TOKEN')
])->post($this->webhookUrl, $data);
```

---

## 📝 الملخص

**ما تم إعداده:**
1. ✅ n8n Workflow مع Webhook
2. ✅ Gmail Node لإرسال الإيميلات
3. ✅ WhatsApp Node (Twilio) للرسائل
4. ✅ Laravel Service للإشعارات
5. ✅ تكامل تلقائي عند إنشاء Quote

**النتيجة:**
```
عند كل عرض سعر جديد:
  ✉️ إيميل فوري للإدارة
  📱 رسالة واتساب فورية
  🔔 إشعارات تلقائية بالكامل
```

---

**تاريخ التوثيق:** 12 أكتوبر 2025
**الحالة:** جاهز للتنفيذ
