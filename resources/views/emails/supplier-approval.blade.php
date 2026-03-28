<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تمت الموافقة على حسابك</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            direction: rtl;
        }
        .container {
            max-width: 650px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 50%, #A855F7 100%);
            color: #ffffff;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .header .subtitle {
            font-size: 16px;
            opacity: 0.95;
            margin: 0;
        }
        .content {
            padding: 40px 30px;
            text-align: center;
            direction: rtl;
        }
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
            direction: rtl;
        }
        .message {
            font-size: 16px;
            color: #555555;
            line-height: 1.8;
            margin-bottom: 25px;
            text-align: center;
            direction: rtl;
        }
        .success-box {
            background: linear-gradient(135deg, #f8f4ff 0%, #fff 100%);
            border: 2px solid #A855F7;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            text-align: center;
            box-shadow: 0 6px 18px rgba(168, 85, 247, 0.1);
        }
        .success-box .icon {
            font-size: 56px;
            margin-bottom: 15px;
        }
        .success-box h3 {
            color: #5B21B6;
            margin: 0 0 10px 0;
            font-size: 20px;
        }
        .success-box p {
            color: #666666;
            margin: 0;
            font-size: 15px;
        }
        .success-box .date-line {
            margin-top: 12px;
            color: #A855F7;
            font-weight: bold;
            font-size: 14px;
        }
        .info-panel {
            background-color: #f8f9fa;
            border-right: 4px solid #5B21B6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            text-align: center;
            direction: rtl;
        }
        .info-panel h4 {
            color: #5B21B6;
            margin: 0 0 15px 0;
            font-size: 17px;
        }
        .info-panel ul {
            margin: 10px 0 0;
            padding-right: 0;
            color: #555555;
            text-align: center;
            direction: rtl;
        }
        .info-panel li {
            margin-bottom: 10px;
            line-height: 1.6;
            text-align: center;
        }
        .cta-button {
            display: inline-block;
            background-color: #ffffff;
            color: #5B21B6 !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            margin: 20px 0;
            box-shadow: 0 4px 15px rgba(91, 33, 182, 0.3);
            transition: all 0.3s ease;
            text-align: center;
            direction: rtl;
            border: 2px solid #5B21B6;
        }
        .cta-button:hover {
            background-color: #ffffff;
            border-color: #A855F7;
            box-shadow: 0 6px 20px rgba(168, 85, 247, 0.35);
            transform: translateY(-2px);
            color: #A855F7 !important;
        }
        .credentials-box {
            background-color: #f3e8ff;
            border: 1px solid #A855F7;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .credentials-box .label {
            color: #5B21B6;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .credentials-box .value {
            color: #333333;
            font-size: 16px;
            margin-bottom: 15px;
            word-break: break-word;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to left, transparent, #A855F7, transparent);
            margin: 30px 0;
        }
        .footer {
            background: linear-gradient(135deg, #faf5ff 0%, #f3e8ff 100%);
            padding: 35px;
            text-align: center;
            border-top: 2px solid #A855F7;
        }
        .footer-text {
            color: #4b5563;
            font-size: 14px;
            margin: 8px 0;
            line-height: 1.6;
        }
        .footer-text strong {
            color: #5B21B6;
        }
        .footer-link {
            color: #5B21B6;
            text-decoration: none;
            font-weight: bold;
        }
        .footer-link:hover {
            color: #A855F7;
        }
        .contact-info {
            margin-top: 25px;
            padding: 20px;
            border-top: 0;
            background-color: #ffffff;
            border: 1px solid #ede9fe;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(168, 85, 247, 0.08);
        }
        @media only screen and (max-width: 600px) {
            .container {
                margin: 20px 10px;
            }
            .header, .content, .footer {
                padding: 30px 20px;
            }
            .cta-button {
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 مبروك! تمت الموافقة على حسابك</h1>
            <p class="subtitle">نرحّب بك ضمن شبكة شركائنا – جاهزين ننطلق!</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">مرحباً {{ $supplierName ?? 'عزيزي المورد' }}،</p>

            <div class="success-box">
                <div class="icon">✅</div>
                <h3>تم قبول طلبك بنجاح!</h3>
                <p>تمت الموافقة على حسابك كمورد لدى {{ $companyName ?? 'Your Events' }}</p>
                <div class="date-line">📅 تاريخ الموافقة: {{ $approvalDate ?? now()->format('Y/m/d H:i') }}</div>
            </div>

            <p class="message">
                يسعدنا إبلاغك أنه تم مراجعة بياناتك والتأكد من توافقها مع معاييرنا المهنية. 
                أنت الآن جزء من شبكة موردينا المعتمدين ويمكنك البدء في استقبال طلبات العملاء.
            </p>
            <h4 style="margin:0 0 10px 0; color:#5B21B6;">🚀 الخطوات التالية:</h4>
            <ul>
                <li>قم بتسجيل الدخول إلى لوحة التحكم الخاصة بك</li>
                <li>أضف خدماتك وحدّث معلومات ملفك الشخصي</li>
                <li>ابدأ في استقبال طلبات عروض الأسعار من العملاء</li>
                <li>تفاعل مع الطلبات واقبل العروض المناسبة</li>
            </ul>

            <div style="text-align: center;">
                <a href="{{ $dashboardUrl ?? route('supplier.login') }}" class="cta-button">
                    🔑 الدخول إلى لوحة المورد
                </a>
            </div>

            @if(isset($supplierEmail))
            <div class="credentials-box">
                <div class="label">📧 البريد الإلكتروني المسجل:</div>
                <div class="value">{{ $supplierEmail }}</div>
                <div class="label">💡 ملاحظة:</div>
                <div class="value" style="font-size: 14px; color: #666;">
                    استخدم هذا البريد الإلكتروني وكلمة المرور التي قمت بتعيينها عند التسجيل للدخول إلى لوحة التحكم.
                </div>
            </div>
            @endif

            <div class="divider"></div>

            <div class="info-panel">
                <h4>💼 مزايا الانضمام لشبكة شركائنا:</h4>
                <ul>
                    <li>الوصول لقاعدة كبيرة من العملاء المهتمين بخدماتك</li>
                    <li>نظام منافسة عادل وشفاف بين الموردين</li>
                    <li>إدارة سهلة لعروض الأسعار والطلبات</li>
                    <li>دعم فني متواصل لمساعدتك على النجاح</li>
                    <li>تقارير وإحصائيات تفصيلية لأعمالك</li>
                </ul>
            </div>

            <div class="info-panel" style="border-right-color:#7C3AED; background:#faf5ff;">
                <h4>📜 الشروط والأحكام</h4>
                <p style="margin:0 0 12px 0; color:#555; line-height:1.8;">
                    باستخدامك لحساب المورد، فأنت توافق على الالتزام بسياسات المنصة وشروط الاستخدام والخصوصية.
                </p>
                <div style="display:flex; gap:10px; justify-content:center; flex-wrap:wrap;">
                    <a href="{{ $termsUrl ?? url('/terms-and-conditions') }}" class="cta-button" style="padding:10px 18px; font-size:14px; margin:0;">عرض الشروط والأحكام</a>
                    <a href="{{ $privacyUrl ?? url('/privacy') }}" class="cta-button" style="padding:10px 18px; font-size:14px; margin:0;">سياسة الخصوصية</a>
                </div>
            </div>

            <p class="message">
                نحن هنا لدعمك في كل خطوة. إذا كان لديك أي استفسار أو تحتاج إلى مساعدة، 
                لا تتردد في التواصل معنا.
            </p>

            <p class="message" style="color: #5B21B6; font-weight: bold; text-align: center; font-size: 18px;">
                نتمنى لك تجربة ناجحة ومثمرة معنا! 🌟
            </p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                <strong>{{ $companyName ?? 'Your Events' }}</strong><br>
                لا وسطاء، لا انتظار، ولا مكالمات ما تخلص. كل شي واضح، وسلس، وسريع
            </p>
            
            <div class="contact-info">
                <p class="footer-text">
                    📧 البريد الإلكتروني: 
                    <a href="mailto:{{ $supportEmail ?? 'hello@yourevents.sa' }}" class="footer-link">
                        {{ $supportEmail ?? 'hello@yourevents.sa' }}
                    </a>
                </p>
                <p class="footer-text">
                    📱 الهاتف: 
                    <a href="tel:+966505159616" class="footer-link" style="direction: ltr; unicode-bidi: bidi-override;">
                        +966 50 515 9616
                    </a>
                </p>
                <p class="footer-text">
                    🌐 الموقع الإلكتروني: 
                    <a href="https://yourevents.sa" class="footer-link">yourevents.sa</a>
                </p>
            </div>

            <p class="footer-text" style="margin-top: 25px; font-size: 12px; color: #999999;">
                © {{ date('Y') }} {{ $companyName ?? 'Your Events' }}. جميع الحقوق محفوظة.<br>
                هذه رسالة تلقائية، يرجى عدم الرد عليها.
            </p>
        </div>
    </div>
</body>
</html>
