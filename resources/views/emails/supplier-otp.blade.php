<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>رمز التحقق</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 28px;
            font-weight: bold;
        }
        .header p {
            color: #ffffff;
            margin: 10px 0 0 0;
            font-size: 16px;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
        }
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
        }
        .message {
            font-size: 16px;
            color: #666666;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        .otp-box {
            background: linear-gradient(135deg, #fff9e6 0%, #fff4cc 100%);
            border: 2px dashed #ffc107;
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
        }
        .otp-label {
            font-size: 14px;
            color: #666666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: bold;
            color: #ff9800;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .otp-validity {
            font-size: 14px;
            color: #d32f2f;
            margin-top: 15px;
            font-weight: bold;
        }
        .warning {
            background-color: #fff3cd;
            border-right: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 30px 0;
            text-align: right;
            border-radius: 8px;
        }
        .warning-title {
            color: #856404;
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 8px;
        }
        .warning-text {
            color: #856404;
            font-size: 14px;
            line-height: 1.5;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e9ecef;
        }
        .footer-text {
            color: #666666;
            font-size: 14px;
            margin: 5px 0;
        }
        .footer-link {
            color: #ffc107;
            text-decoration: none;
            font-weight: bold;
        }
        .footer-link:hover {
            color: #ff9800;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 10px;
            color: #666666;
            text-decoration: none;
            font-size: 20px;
        }
        .social-links a:hover {
            color: #ffc107;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                margin: 20px 10px;
            }
            .header, .content, .footer {
                padding: 30px 20px;
            }
            .otp-code {
                font-size: 32px;
                letter-spacing: 4px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 منصة فعالياتك</h1>
            <p>رمز التحقق لتسجيل المورد</p>
        </div>

        <!-- Content -->
        <div class="content">
            @if($supplierName)
            <div class="greeting">
                مرحباً {{ $supplierName }}،
            </div>
            @else
            <div class="greeting">
                مرحباً بك،
            </div>
            @endif

            <div class="message">
                شكراً لتسجيلك كمورد في منصة فعالياتك. لإتمام عملية التسجيل، يرجى استخدام رمز التحقق التالي:
            </div>

            <!-- OTP Box -->
            <div class="otp-box">
                <div class="otp-label">رمز التحقق الخاص بك</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-validity">⏰ صالح لمدة 10 دقائق فقط</div>
            </div>

            <!-- Warning -->
            <div class="warning">
                <div class="warning-title">⚠️ تنبيه أمني مهم</div>
                <div class="warning-text">
                    • لا تشارك هذا الرمز مع أي شخص آخر<br>
                    • فريق فعالياتك لن يطلب منك هذا الرمز أبداً<br>
                    • إذا لم تقم بطلب هذا الرمز، يرجى تجاهل هذه الرسالة
                </div>
            </div>

            <div class="message">
                إذا واجهت أي مشكلة في التسجيل أو لديك أي استفسار، لا تتردد في التواصل معنا.
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                <strong>منصة فعالياتك</strong><br>
                منصتك المتكاملة لتنظيم الفعاليات والمناسبات
            </p>
            <p class="footer-text">
                📧 <a href="mailto:support@your-events.com" class="footer-link">support@your-events.com</a><br>
                📱 <a href="tel:+966500000000" class="footer-link">+966 50 000 0000</a>
            </p>
            
            <div class="social-links">
                <a href="#" title="تويتر">🐦</a>
                <a href="#" title="إنستجرام">📷</a>
                <a href="#" title="سناب شات">👻</a>
                <a href="#" title="تيك توك">🎵</a>
            </div>

            <p class="footer-text" style="margin-top: 20px; font-size: 12px; color: #999999;">
                © {{ date('Y') }} منصة فعالياتك. جميع الحقوق محفوظة.<br>
                هذه رسالة تلقائية، يرجى عدم الرد عليها.
            </p>
        </div>
    </div>
</body>
</html>
