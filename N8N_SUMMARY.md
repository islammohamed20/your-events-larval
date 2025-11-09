# 📱 دمج n8n: إشعارات Gmail + واتساب عند عرض السعر

## 🎯 الهدف
عند إنشاء عرض سعر جديد → n8n يرسل:
1. ✉️ إيميل Gmail للإدارة
2. 📱 رسالة واتساب للإدارة

---

## ✅ جزء Laravel (مكتمل ✓)

```
✓ إنشاء N8nNotificationService
✓ تعديل QuoteController
✓ إضافة config/services.php
✓ إضافة N8N_WEBHOOK_URL في .env
```

---

## 🔧 الخطوات المتبقية (n8n)

### 1️⃣ افتح n8n
```
http://localhost:5678
```

### 2️⃣ أنشئ Workflow جديد
- اسمه: "Laravel Quote Notifications"

### 3️⃣ أضف Webhook
- Node: Webhook
- Method: POST
- Path: `quote-created`
- انسخ الرابط: `http://localhost:5678/webhook/quote-created`

### 4️⃣ حدّث Laravel .env
```env
N8N_WEBHOOK_URL=http://localhost:5678/webhook/quote-created
```
ثم:
```bash
php artisan config:clear
```

### 5️⃣ أضف Gmail Node
- ربط حساب Gmail (OAuth)
- To: `admin@your-events.com`
- Subject: العنوان من الملف الكامل
- Body: HTML من الملف الكامل

### 6️⃣ أضف WhatsApp (Twilio)
- حساب Twilio مجاني
- HTTP Request Node
- URL Twilio API
- From: رقم Twilio
- To: رقمك (+966...)

### 7️⃣ فعّل Workflow
- Active → أخضر ✅

---

## 🧪 اختبار

```bash
# Test من Terminal
curl -X POST http://localhost:5678/webhook/quote-created \
  -H "Content-Type: application/json" \
  -d '{"quote_number":"TEST","customer_name":"اختبار"}'
```

**أو** أنشئ عرض سعر من الموقع مباشرة!

---

## 📚 التفاصيل الكاملة

راجع الملفات:
- `N8N_QUICK_SETUP.md` - خطوات مفصلة
- `N8N_INTEGRATION_GUIDE.md` - دليل كامل

---

**الحالة:** Laravel جاهز، n8n ينتظر الإعداد
**الوقت:** 20 دقيقة
