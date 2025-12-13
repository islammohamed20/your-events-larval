# 🔐 OTP System - Visual Flow Guide

## 📊 تدفق النظام البصري

```
┌─────────────────────────────────────────────────────────────────┐
│                    🏁 نظام OTP - تدفق كامل                      │
└─────────────────────────────────────────────────────────────────┘

┌──────────────┐
│  المستخدم    │
│   يدخل      │
│   البريد     │
└──────┬───────┘
       │
       ▼
┌──────────────────────┐
│  POST /otp/send      │
│  ────────────────    │
│  email: user@...     │
│  type: email_ver...  │
└──────┬───────────────┘
       │
       ▼
┌─────────────────────────────┐
│  OtpController@sendOtp      │
│  ─────────────────────      │
│  1. Validation ✓            │
│  2. Rate Limiting (3/5min)  │
│  3. Check Email Existence   │
└──────┬──────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  OtpVerification::generate()    │
│  ────────────────────────────   │
│  1. Delete old OTPs             │
│  2. Generate Random 6-digit     │
│  3. Save to Database            │
│  4. Send Email ✉️               │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  📧 البريد الإلكتروني           │
│  ────────────────────────       │
│  🔐 كود: 123456                 │
│  ⏱️ صالح لمدة: 10 دقائق        │
│  🎯 الغرض: التحقق من البريد     │
└──────┬──────────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│  المستخدم يستلم البريد       │
│  وينسخ الكود                 │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│  GET /verify-otp             │
│  ────────────────            │
│  عرض صفحة إدخال الكود       │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│  👤 المستخدم يدخل الكود      │
│  [ 1 ][ 2 ][ 3 ][ 4 ][ 5 ][ 6 ] │
└──────┬───────────────────────┘
       │
       ▼
┌──────────────────────────────┐
│  POST /otp/verify            │
│  ────────────────            │
│  email: user@...             │
│  otp: 123456                 │
│  type: email_verification    │
└──────┬───────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  OtpController@verifyOtp        │
│  ────────────────────────       │
│  1. Validation ✓                │
│  2. Rate Limiting (5/1min)      │
│  3. Find OTP Record             │
│  4. Increment Attempts          │
└──────┬──────────────────────────┘
       │
       ▼
┌─────────────────────────────────┐
│  OtpVerification::verify()      │
│  ────────────────────────       │
│  1. Check Code Match ✓          │
│  2. Check Not Expired ✓         │
│  3. Check Attempts < 5 ✓        │
│  4. Update Status → verified    │
└──────┬──────────────────────────┘
       │
       ├─── ✅ Success ───────────────┐
       │                              │
       │                              ▼
       │                    ┌──────────────────┐
       │                    │  Session Storage │
       │                    │  ────────────    │
       │                    │  otp_verified: ✓ │
       │                    │  otp_email: ...  │
       │                    └────────┬─────────┘
       │                             │
       │                             ▼
       │                    ┌──────────────────┐
       │                    │  Redirect to     │
       │                    │  Next Step       │
       │                    │  ────────────    │
       │                    │  • Registration  │
       │                    │  • Dashboard     │
       │                    │  • Reset Pass    │
       │                    └──────────────────┘
       │
       └─── ❌ Failed ────────────────┐
                                      │
                                      ▼
                            ┌──────────────────┐
                            │  Error Response  │
                            │  ────────────    │
                            │  • Wrong Code    │
                            │  • Expired       │
                            │  • Too Many Try  │
                            └──────────────────┘
```

---

## 🗂️ هيكل قاعدة البيانات

```
┌─────────────────────────────────────────────────────────┐
│                otp_verifications Table                  │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  id               BIGINT (PK)                           │
│  email            VARCHAR(255) [INDEX]                  │
│  otp              VARCHAR(6)                            │
│  type             ENUM [INDEX]                          │
│                   • email_verification                  │
│                   • login                               │
│                   • password_reset                      │
│                   • booking_confirmation                │
│                   • payment_confirmation                │
│  status           ENUM [INDEX]                          │
│                   • pending                             │
│                   • verified                            │
│                   • expired                             │
│                   • failed                              │
│  expires_at       TIMESTAMP [INDEX]                     │
│  verified_at      TIMESTAMP (nullable)                  │
│  attempts         INTEGER (default: 0)                  │
│  ip_address       VARCHAR(45) (nullable)                │
│  user_agent       TEXT (nullable)                       │
│  created_at       TIMESTAMP                             │
│  updated_at       TIMESTAMP                             │
│                                                         │
└─────────────────────────────────────────────────────────┘

Indexes:
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
• email
• (email, type)
• (email, status)
• expires_at
```

---

## 📧 قالب البريد الإلكتروني

```
┌───────────────────────────────────────────────────┐
│                                                   │
│        🎨 Gradient Header (Purple)                │
│                                                   │
│              🔐                                   │
│           كود التحقق                              │
│                                                   │
├───────────────────────────────────────────────────┤
│                                                   │
│  مرحباً،                                         │
│                                                   │
│  تلقيت هذا البريد لأنك طلبت كود تحقق            │
│  لـ [التحقق من البريد الإلكتروني]               │
│                                                   │
│  ┌─────────────────────────────────────┐         │
│  │                                     │         │
│  │       🔐  1  2  3  4  5  6         │         │
│  │                                     │         │
│  └─────────────────────────────────────┘         │
│                                                   │
│  ┌────────────────────────────────────┐          │
│  │ ⏱️ صلاحية الكود: 10 دقيقة         │          │
│  │ 🎯 الغرض: التحقق من البريد         │          │
│  │ 📧 البريد: user@example.com       │          │
│  └────────────────────────────────────┘          │
│                                                   │
│  ⚠️ تنبيه أمني: لا تشارك هذا الكود مع أحد       │
│                                                   │
│  إذا لم تطلب هذا الكود، يرجى تجاهل البريد       │
│                                                   │
├───────────────────────────────────────────────────┤
│  Footer (Gray Background)                         │
│  ─────────────────────────                        │
│  Your Events                                      │
│  منصة حجز الفعاليات والخدمات                     │
│  📧 hello@yourevents.sa | 📱 0500000000           │
│  © 2025 Your Events. جميع الحقوق محفوظة          │
└───────────────────────────────────────────────────┘
```

---

## 🎨 صفحة إدخال OTP

```
┌────────────────────────────────────────────────┐
│                                                │
│         🎨 Gradient Header (Purple)            │
│                  🔐                            │
│         التحقق من البريد الإلكتروني           │
│                                                │
├────────────────────────────────────────────────┤
│                                                │
│  ℹ️ تم إرسال كود التحقق إلى:                 │
│     user@example.com                           │
│                                                │
│  🔑 كود التحقق:                               │
│                                                │
│  ┌───┐ ┌───┐ ┌───┐ ┌───┐ ┌───┐ ┌───┐        │
│  │ 1 │ │ 2 │ │ 3 │ │ 4 │ │ 5 │ │ 6 │        │
│  └───┘ └───┘ └───┘ └───┘ └───┘ └───┘        │
│                                                │
│  ⏱️ سينتهي الكود خلال: 09:45                 │
│                                                │
│  ┌────────────────────────────────────┐       │
│  │   ✓ تحقق من الكود                 │       │
│  └────────────────────────────────────┘       │
│                                                │
│  لم تستلم الكود؟                              │
│  🔄 إعادة إرسال الكود (60)                   │
│                                                │
│  ┌──────────────────────────────────┐         │
│  │ 💡 نصائح:                        │         │
│  │ • تحقق من البريد المزعج          │         │
│  │ • الكود صالح 10 دقائق            │         │
│  │ • لديك 5 محاولات                 │         │
│  │ • لا تشارك الكود                 │         │
│  └──────────────────────────────────┘         │
│                                                │
└────────────────────────────────────────────────┘
```

---

## 🔄 حالات OTP

```
┌─────────────┐
│   pending   │ ◄─── Initial State (عند الإنشاء)
└──────┬──────┘
       │
       ├─── ✅ التحقق نجح ───────► verified
       │
       ├─── ❌ انتهت الصلاحية ───► expired
       │
       ├─── ❌ تجاوز المحاولات ──► failed
       │
       └─── 🔄 إعادة إرسال ──────► حذف + إنشاء جديد


Status Flow:
═══════════════════════════════════════════════════

pending → verified     ✅ Success (تم التحقق)
pending → expired      ⏱️ Time Out (انتهت الصلاحية)
pending → failed       ❌ Too Many Attempts (محاولات كثيرة)
pending → deleted      🗑️ Resend Requested (إعادة إرسال)
```

---

## 📊 Rate Limiting

```
┌──────────────────────────────────────────────────┐
│              Rate Limiting Rules                 │
├──────────────────────────────────────────────────┤
│                                                  │
│  📤 إرسال OTP (POST /otp/send)                  │
│  ────────────────────────────────               │
│  Limit: 3 attempts per 5 minutes                │
│  Key: 'send-otp:{email}'                        │
│                                                  │
│  Exceeded Response:                              │
│  HTTP 429 Too Many Requests                      │
│  "تم تجاوز الحد الأقصى. حاول بعد X ثانية"      │
│                                                  │
├──────────────────────────────────────────────────┤
│                                                  │
│  ✅ التحقق من OTP (POST /otp/verify)            │
│  ────────────────────────────────────           │
│  Limit: 5 attempts per 1 minute                 │
│  Key: 'verify-otp:{email}'                      │
│                                                  │
│  Exceeded Response:                              │
│  HTTP 429 Too Many Requests                      │
│  "تم تجاوز الحد الأقصى. حاول لاحقاً"            │
│                                                  │
├──────────────────────────────────────────────────┤
│                                                  │
│  🔄 إعادة إرسال OTP (POST /otp/resend)          │
│  ────────────────────────────────────────       │
│  Limit: 3 attempts per 5 minutes                │
│  Key: 'send-otp:{email}'                        │
│  (Same as send)                                 │
│                                                  │
└──────────────────────────────────────────────────┘
```

---

## 🔒 Security Checklist

```
✅ Random OTP Generation
   └─► random_int(100000, 999999)
   
✅ Expiry Mechanism
   └─► expires_at = now() + 10 minutes
   
✅ Single Use Enforcement
   └─► status: pending → verified (one time only)
   
✅ Attempt Limiting
   └─► max 5 attempts → status: failed
   
✅ Rate Limiting
   └─► Send: 3/5min, Verify: 5/1min
   
✅ Activity Logging
   └─► IP Address + User Agent
   
✅ Secure Email
   └─► HTTPS, TLS Encryption
   
✅ Session Management
   └─► otp_verified flag in session
   
✅ Input Validation
   └─► Email format, OTP length
   
✅ CSRF Protection
   └─► @csrf token in all forms
```

---

## 🎯 Use Cases

```
┌─────────────────────────────────────────────────┐
│  1. التسجيل الجديد (email_verification)         │
├─────────────────────────────────────────────────┤
│  User → Enter Email                             │
│      → Receive OTP                              │
│      → Verify OTP                               │
│      → Complete Registration                    │
│      → Account Created ✅                       │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  2. تسجيل الدخول (login)                        │
├─────────────────────────────────────────────────┤
│  User → Enter Email                             │
│      → Receive OTP                              │
│      → Verify OTP                               │
│      → Logged In ✅                             │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  3. إعادة تعيين كلمة المرور (password_reset)    │
├─────────────────────────────────────────────────┤
│  User → Forgot Password                         │
│      → Enter Email                              │
│      → Receive OTP                              │
│      → Verify OTP                               │
│      → Set New Password                         │
│      → Password Changed ✅                      │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  4. تأكيد الحجز (booking_confirmation)          │
├─────────────────────────────────────────────────┤
│  User → Create Booking                          │
│      → Receive OTP                              │
│      → Verify OTP                               │
│      → Booking Confirmed ✅                     │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  5. تأكيد الدفع (payment_confirmation)          │
├─────────────────────────────────────────────────┤
│  User → Initiate Payment                        │
│      → Receive OTP                              │
│      → Verify OTP                               │
│      → Payment Processed ✅                     │
└─────────────────────────────────────────────────┘
```

---

## 📁 File Structure

```
your-events/
│
├── app/
│   ├── Models/
│   │   └── OtpVerification.php ✅
│   │       ├── generate()
│   │       ├── verify()
│   │       ├── cleanExpired()
│   │       ├── incrementAttempts()
│   │       ├── isExpired()
│   │       └── isValid()
│   │
│   └── Http/
│       └── Controllers/
│           └── OtpController.php ✅
│               ├── showVerifyForm()
│               ├── sendOtp()
│               ├── verifyOtp()
│               ├── resendOtp()
│               ├── completeRegistration()
│               └── cleanExpired()
│
├── database/
│   └── migrations/
│       └── *_create_otp_verifications_table.php ✅
│
├── resources/
│   └── views/
│       ├── auth/
│       │   ├── verify-otp.blade.php ✅
│       │   └── register-complete.blade.php ✅
│       └── otp-test.blade.php ✅
│
├── routes/
│   └── web.php ✅
│       ├── GET  /verify-otp
│       ├── POST /otp/send
│       ├── POST /otp/verify
│       ├── POST /otp/resend
│       ├── GET  /register/complete
│       ├── POST /register/complete
│       └── GET  /otp-test
│
└── Documentation/ ✅
    ├── OTP-SYSTEM-GUIDE.md
    ├── OTP-QUICK-START.md
    ├── OTP-IMPLEMENTATION-SUMMARY.md
    ├── README-OTP.md
    └── OTP-VISUAL-FLOW-GUIDE.md (هذا الملف)
```

---

## 🚀 Quick Commands

```bash
# اختبار OTP
php artisan tinker
>>> $otp = App\Models\OtpVerification::generate('test@example.com');
>>> echo $otp->otp;

# التحقق
>>> $result = App\Models\OtpVerification::verify('test@example.com', '123456', 'email_verification');
>>> print_r($result);

# تنظيف الأكواد القديمة
>>> App\Models\OtpVerification::cleanExpired();

# عرض الإحصائيات
>>> OtpVerification::selectRaw('status, count(*) as count')->groupBy('status')->get();
```

---

## 📊 Dashboard Stats

```
┌─────────────────────────────────────────────┐
│         📊 إحصائيات OTP                     │
├─────────────────────────────────────────────┤
│                                             │
│  📧 إجمالي الأكواد          150            │
│  ✅ تم التحقق               120 (80%)      │
│  ⏳ قيد الانتظار            20  (13%)      │
│  ⏱️ منتهية الصلاحية         8   (5%)       │
│  ❌ فاشلة                    2   (2%)       │
│                                             │
│  📈 معدل النجاح: 80%                       │
│  🕐 متوسط وقت التحقق: 2.5 دقيقة           │
│  📅 اليوم: 25 كود                          │
│                                             │
└─────────────────────────────────────────────┘
```

---

## 🎉 النتيجة النهائية

```
┌───────────────────────────────────────────┐
│        ✅ نظام OTP جاهز 100%             │
├───────────────────────────────────────────┤
│                                           │
│  ✓ قاعدة البيانات                        │
│  ✓ Backend Logic                         │
│  ✓ Frontend UI                           │
│  ✓ Email Templates                       │
│  ✓ Security                              │
│  ✓ Rate Limiting                         │
│  ✓ Documentation                         │
│  ✓ Test Page                             │
│                                           │
│  🚀 جاهز للاستخدام الفوري!              │
│                                           │
└───────────────────────────────────────────┘
```

---

**🔐 نظام OTP - آمن، سريع، وسهل الاستخدام!** ✨
