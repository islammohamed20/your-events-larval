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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: 2px dashed #ffc107;
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
        }
        .otp-label {
            font-size: 14px;
            color: #ffffffff;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: bold;
            color: #ffffffff;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 10px 0;
        }
        .otp-validity {
            font-size: 14px;
            color: #ffffffff;
            margin-top: 15px;
            font-weight: bold;
        }
        .warning {
            background-color: #fff3cd;
            border-right: 4px solid #ffc107;
            padding: 15px 20px;
            margin: 30px 0;
            text-align: center;
            border-radius: 8px;
            direction: rtl;
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
            text-align: center;
            direction: rtl;
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
            <h1>Your Events</h1>
            <p>لا وسطاء، لا انتظار، ولا مكالمات ما تخلص. كل شي واضح، وسلس، وسريع</p>
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
                شكراً لتسجيلك كمورد في منصة Your Events. لإتمام عملية التسجيل، يرجى استخدام رمز التحقق التالي:
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
        <div class="footer" style="direction: rtl; text-align: center;">
            <p class="footer-text">
                <strong>Your Events</strong><br>
                لا وسطاء، لا انتظار، ولا مكالمات ما تخلص. كل شي واضح، وسلس، وسريع
            </p>
            <p class="footer-text">
                📧 <a href="mailto:hello@yourevents.sa" class="footer-link">hello@yourevents.sa</a><br>
                📱 <a href="tel:+966505159616" class="footer-link" style="direction: ltr; unicode-bidi: bidi-override;">+966 50 515 9616</a>
            </p>
            
            <p class="footer-text" style="margin-top: 20px; font-size: 12px; color: #999999;">
                © {{ date('Y') }} Your Events. جميع الحقوق محفوظة.<br>
                هذه رسالة تلقائية، يرجى عدم الرد عليها.
            </p>
        </div>
    </div>
</body>
</html>
