# 🚀 الخطوات العملية لتفعيل n8n مع Laravel

## ✅ ما تم في Laravel (مكتمل)

```
✅ إنشاء N8nNotificationService
✅ إضافة إعدادات n8n في config/services.php
✅ إضافة N8N_WEBHOOK_URL في .env
✅ تحديث QuoteController
✅ مسح الـ cache
```

---

## 📋 الخطوات المتبقية (في n8n)

### الخطوة 1: الوصول إلى n8n
```bash
# افتح المتصفح:
http://localhost:5678
# أو
http://72.61.154.100:5678
```

---

### الخطوة 2: إنشاء Workflow جديد

1. اضغط **"+ New workflow"**
2. اسم الـ workflow: **"Laravel Quote Notifications"**
3. احفظ (Ctrl+S)

---

### الخطوة 3: إضافة Webhook Node

#### أ) إضافة Node:
```
1. اضغط "+" في الـ canvas
2. ابحث عن "Webhook"
3. اختر "Webhook"
```

#### ب) إعدادات الـ Webhook:
```
- HTTP Method: POST
- Path: quote-created
- Response Mode: Immediately
- احفظ
```

#### ج) نسخ Webhook URL:
بعد الحفظ، ستجد URL مثل:
```
http://localhost:5678/webhook/quote-created
```

**📝 هام جداً:** انسخ هذا الرابط!

---

### الخطوة 4: تحديث Laravel .env

افتح ملف `.env` وعدّل:
```env
N8N_WEBHOOK_URL=http://localhost:5678/webhook/quote-created
```

إذا كان n8n على نفس السيرفر، استخدم:
```env
N8N_WEBHOOK_URL=http://127.0.0.1:5678/webhook/quote-created
```

ثم:
```bash
cd /var/www/your-events
php artisan config:clear
```

---

### الخطوة 5: اختبار الـ Webhook

#### في n8n:
1. اضغط **"Listen for Test Event"** على Webhook node
2. سيظهر: "Waiting for test event..."

#### في Terminal (على السيرفر):
```bash
curl -X POST http://localhost:5678/webhook/quote-created \
  -H "Content-Type: application/json" \
  -d '{
    "quote_id": 1,
    "quote_number": "QT-TEST-001",
    "customer_name": "عبدالله محمد",
    "customer_email": "test@example.com",
    "customer_phone": "0501234567",
    "total": "5,000.00",
    "items_count": 3,
    "customer_notes": "test",
    "created_at": "2025-10-12 10:30:00",
    "quote_url": "http://72.61.154.100/admin/quotes/1"
  }'
```

**النتيجة المتوقعة:** ✅ البيانات تظهر في n8n

---

### الخطوة 6: إضافة Gmail Node

#### أ) إضافة Node:
```
1. اضغط "+" بعد Webhook node
2. ابحث عن "Gmail"
3. اختر "Gmail"
```

#### ب) إعدادات Gmail:
```
- Operation: "Send Email"
- Resource: "Message"
```

#### ج) Credentials (أول مرة فقط):

**1. إنشاء Google Cloud Project:**
```
→ اذهب إلى: https://console.cloud.google.com/
→ New Project: "Your Events"
→ Enable APIs: Gmail API
```

**2. إنشاء OAuth Credentials:**
```
→ APIs & Services → Credentials
→ Create Credentials → OAuth client ID
→ Application type: Web application
→ Name: "n8n Gmail"
→ Authorized redirect URIs:
   http://localhost:5678/rest/oauth2-credential/callback
   http://72.61.154.100:5678/rest/oauth2-credential/callback
→ Create
→ Copy: Client ID + Client Secret
```

**3. في n8n Gmail Node:**
```
→ Credentials → Create New
→ Name: "Gmail Account"
→ Paste Client ID
→ Paste Client Secret
→ Connect My Account
→ Sign in with Gmail
→ Allow permissions
```

#### د) محتوى الإيميل:

**To:**
```
admin@your-events.com
```
(أو البريد الإلكتروني للإدارة)

**Subject:**
```
🔔 عرض سعر جديد من {{ $json.customer_name }} - {{ $json.quote_number }}
```

**Email Format:** HTML

**Message (HTML):**
```html
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background-color: white; border-radius: 10px; padding: 30px; max-width: 600px; margin: 0 auto; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #ef4870 0%, #ff7ba3 100%); color: white; padding: 20px; border-radius: 10px 10px 0 0; margin: -30px -30px 20px -30px; }
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
                <p>{{ $json.customer_notes }}</p>
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
```

---

### الخطوة 7: إضافة WhatsApp Node (Twilio)

#### أ) إنشاء حساب Twilio:
```
1. اذهب إلى: https://www.twilio.com/
2. Sign Up (مجاني للتجربة)
3. احصل على:
   - Account SID
   - Auth Token
   - WhatsApp Test Number
```

#### ب) إعداد WhatsApp Sandbox:
```
1. في Twilio Console:
   Messaging → Try it out → Send a WhatsApp message
2. اتبع التعليمات:
   - أرسل الكود من واتساب الخاص بك
   - إلى رقم Twilio WhatsApp Sandbox
3. مثال: "join YOUR-CODE"
```

#### ج) في n8n - إضافة HTTP Request Node:
```
1. اضغط "+" بعد Gmail node
2. ابحث عن "HTTP Request"
3. اختر "HTTP Request"
```

#### د) إعدادات HTTP Request:

**Method:** POST

**URL:**
```
https://api.twilio.com/2010-04-01/Accounts/YOUR_ACCOUNT_SID/Messages.json
```
(استبدل YOUR_ACCOUNT_SID برقم الحساب من Twilio)

**Authentication:** Basic Auth
```
- User: YOUR_ACCOUNT_SID
- Password: YOUR_AUTH_TOKEN
```

**Body Content Type:** Form-Data

**Body Parameters (Specify Body):**
```json
{
  "From": "whatsapp:+14155238886",
  "To": "whatsapp:+966501234567",
  "Body": "🔔 *عرض سعر جديد!*\n\n*رقم العرض:* {{ $json.quote_number }}\n*العميل:* {{ $json.customer_name }}\n*الجوال:* {{ $json.customer_phone }}\n*المبلغ:* {{ $json.total }} ريال\n*عدد الخدمات:* {{ $json.items_count }}\n\n*ملاحظات العميل:*\n{{ $json.customer_notes }}\n\n🔗 رابط العرض:\n{{ $json.quote_url }}"
}
```

**ملاحظات:**
- `From`: رقم Twilio Sandbox (يبدأ بـ whatsapp:+)
- `To`: رقمك بصيغة whatsapp:+966...

---

### الخطوة 8: تفعيل Workflow

1. احفظ الـ workflow (Ctrl+S)
2. اضغط **"Active"** في أعلى اليسار (يتحول إلى أخضر)
3. الآن الـ workflow يعمل تلقائياً! 🎉

---

## 🧪 الاختبار الكامل

### Test 1: من Terminal
```bash
curl -X POST http://localhost:5678/webhook/quote-created \
  -H "Content-Type: application/json" \
  -d '{
    "quote_number": "TEST-001",
    "customer_name": "اختبار",
    "customer_email": "test@test.com",
    "customer_phone": "0501234567",
    "total": "1,000.00",
    "items_count": 1,
    "customer_notes": "test",
    "created_at": "2025-10-12 10:00:00",
    "quote_url": "http://72.61.154.100/admin/quotes/1"
  }'
```

**المتوقع:**
- ✅ إيميل يصل لـ Gmail
- ✅ رسالة واتساب تصل

### Test 2: من Laravel
```bash
php artisan tinker

# اختبار الإشعار
$quote = \App\Models\Quote::with(['items', 'user'])->first();
app(\App\Services\N8nNotificationService::class)->sendNewQuoteNotification($quote);

# تحقق من الـ logs
tail -f storage/logs/laravel.log
```

### Test 3: اختبار حقيقي
```
1. سجل دخول كعميل في الموقع
2. أضف خدمات للسلة
3. اضغط Checkout
4. املأ الملاحظات
5. أنشئ عرض السعر
```

**المتوقع:**
- ✅ Quote يتم إنشاؤه في Database
- ✅ إيميل يصل للإدارة خلال ثوانٍ
- ✅ رسالة واتساب تصل للإدارة
- ✅ Logs في Laravel تظهر: "n8n notification sent successfully"

---

## 🔍 Troubleshooting

### المشكلة: Webhook لا يستقبل البيانات

**الحل:**
```bash
# 1. تحقق أن n8n يعمل
curl http://localhost:5678/healthz

# 2. تحقق من URL في .env
cat /var/www/your-events/.env | grep N8N

# 3. امسح الـ cache
cd /var/www/your-events
php artisan config:clear
php artisan cache:clear

# 4. تحقق من الـ logs
tail -f /var/www/your-events/storage/logs/laravel.log
```

### المشكلة: Gmail لا يرسل

**الحل:**
```
1. تأكد من Gmail API مُفعّل في Google Cloud
2. تأكد من OAuth Credentials صحيحة
3. جرب Disconnect & Reconnect
4. تأكد من البريد في "To" صحيح
```

### المشكلة: WhatsApp لا يرسل

**الحل:**
```
1. تأكد من Twilio credentials صحيحة
2. تأكد من رقمك مُسجل في Sandbox
3. تحقق من صيغة الرقم: whatsapp:+966...
4. تأكد من رصيد Twilio كافٍ (للحسابات المجانية)
```

---

## 📊 ما يحدث بالضبط؟

```
العميل ينشئ عرض سعر في الموقع
              ↓
Laravel QuoteController::checkout()
              ↓
يحفظ Quote في Database
              ↓
N8nNotificationService::sendNewQuoteNotification()
              ↓
HTTP POST → http://localhost:5678/webhook/quote-created
              ↓
         n8n Workflow
         ↙          ↘
  Gmail Node    WhatsApp Node
      ↓              ↓
  إيميل للإدارة   رسالة واتساب
```

---

## ✅ الخلاصة

**ما تم:**
1. ✅ Laravel جاهز وينتظر n8n
2. ✅ Service class موجود
3. ✅ Integration في QuoteController

**ما يجب عمله الآن:**
1. ⏳ إعداد n8n workflow
2. ⏳ ربط Gmail
3. ⏳ ربط Twilio WhatsApp
4. ⏳ تفعيل الـ workflow
5. ⏳ اختبار النظام

**الوقت المتوقع:** 20-30 دقيقة

---

**آخر تحديث:** 12 أكتوبر 2025
