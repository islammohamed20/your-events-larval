<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فرصة عمل جديدة</title>
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
            margin: 20px auto;
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
        .urgent-badge {
            background-color: #ff4444;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            color: #333333;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: right;
        }
        .highlight-box {
            background: linear-gradient(135deg, #fff5f7 0%, #ffe0e6 100%);
            border: 2px solid #ff4444;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
            font-size: 16px;
            color: #d32f2f;
            font-weight: bold;
        }
        .info-box {
            background-color: #f8f9fa;
            border-right: 4px solid #5B21B6;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
        }
        .info-box h3 {
            color: #5B21B6;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .info-box table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-box table td {
            padding: 8px 0;
            font-size: 15px;
        }
        .info-box table td:first-child {
            color: #666666;
            width: 40%;
        }
        .info-box table td:last-child {
            color: #333333;
            font-weight: 600;
        }
        .services-list {
            background-color: #f3e8ff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
        }
        .services-list h4 {
            color: #5B21B6;
            margin: 0 0 15px 0;
        }
        .service-item {
            background-color: white;
            padding: 12px 15px;
            margin: 8px 0;
            border-radius: 8px;
            border-right: 3px solid #A855F7;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #ff4444 0%, #ff6666 100%);
            color: #ffffff !important;
            padding: 18px 50px;
            text-decoration: none;
            border-radius: 30px;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
            box-shadow: 0 6px 20px rgba(255, 68, 68, 0.4);
            transition: all 0.3s ease;
            text-align: center;
        }
        .cta-button:hover {
            background: linear-gradient(135deg, #ff6666 0%, #ff8888 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 68, 68, 0.5);
        }
        .timer-box {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .timer-box .time {
            font-size: 32px;
            font-weight: bold;
            color: #ff6b00;
            margin: 10px 0;
        }
        .warning-text {
            color: #856404;
            font-size: 14px;
            margin-top: 10px;
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
            margin: 8px 0;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔥 فرصة عمل جديدة!</h1>
            <span class="urgent-badge">⚡ عاجل - المنافسة الآن!</span>
        </div>
        
        <div class="content">
            <p class="greeting">عزيزي/عزيزتي {{ $supplier->name }}،</p>
            
            <div class="highlight-box">
                🎯 طلب جديد يطابق خدماتك!<br>
                <strong>أول مورد يقبل الطلب سيحصل عليه كاملاً!</strong>
            </div>

            <div class="timer-box">
                <div style="font-size: 16px; color: #856404; margin-bottom: 5px;">⏰ الوقت المتبقي للقبول</div>
                <div class="time">{{ $order->time_remaining }}</div>
                <div class="warning-text">⚠️ سينتهي الطلب في: {{ $order->expires_at->format('Y/m/d H:i') }}</div>
            </div>

            <div class="info-box">
                <h3>📋 تفاصيل الطلب #{{ $order->order_number }}</h3>
                <table>
                    <tr>
                        <td>اسم العميل:</td>
                        <td>{{ $order->customer_name }}</td>
                    </tr>
                    <tr>
                        <td>رقم الهاتف:</td>
                        <td><a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></td>
                    </tr>
                    <tr>
                        <td>تاريخ الحدث:</td>
                        <td>{{ $order->event_date->format('Y/m/d') }}</td>
                    </tr>
                    @if($order->event_time)
                    <tr>
                        <td>وقت الحدث:</td>
                        <td>{{ $order->event_time }}</td>
                    </tr>
                    @endif
                    @if($order->event_location)
                    <tr>
                        <td>موقع الحدث:</td>
                        <td>{{ $order->event_location }}</td>
                    </tr>
                    @endif
                    @if($order->guests_count)
                    <tr>
                        <td>عدد الضيوف:</td>
                        <td>{{ $order->guests_count }} ضيف</td>
                    </tr>
                    @endif
                    <tr>
                        <td>عدد الموردين المُشعرين:</td>
                        <td><strong style="color: #ff4444;">{{ $order->notified_suppliers_count }} مورد</strong></td>
                    </tr>
                </table>
            </div>

            <div class="services-list">
                <h4>🎯 الخدمات المطلوبة ({{ $order->services->count() }} خدمة):</h4>
                @foreach($order->services as $service)
                <div class="service-item">
                    <strong>{{ $service->name }}</strong>
                    @if($service->pivot->quantity > 1)
                        <span style="color: #A855F7;"> (الكمية: {{ $service->pivot->quantity }})</span>
                    @endif
                    @if($service->pivot->notes)
                        <div style="font-size: 13px; color: #666; margin-top: 5px;">
                            📝 {{ $service->pivot->notes }}
                        </div>
                    @endif
                </div>
                @endforeach
            </div>

            @if($order->notes)
            <div class="info-box">
                <h3>📝 ملاحظات العميل:</h3>
                <p style="margin: 0; color: #555; line-height: 1.6;">{{ $order->notes }}</p>
            </div>
            @endif

            <div style="text-align: center; margin: 40px 0;">
                <a href="{{ url('/supplier/orders/' . $order->id) }}" class="cta-button">
                    ⚡ قبول الطلب الآن
                </a>
                <p style="color: #999; font-size: 13px; margin-top: 15px;">
                    سيتم قبول أول مورد يضغط على الزر فقط
                </p>
            </div>

            <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0; border-right: 4px solid #ffc107;">
                <strong style="color: #856404;">⚠️ تنبيه مهم:</strong>
                <ul style="margin: 10px 0; padding-right: 20px; color: #856404;">
                    <li>هذا نظام تنافسي - أول مورد يقبل يحصل على الطلب كاملاً</li>
                    <li>حتى لو كنت تقدم خدمة واحدة فقط، ستحصل على جميع الخدمات</li>
                    <li>لا يمكن التراجع بعد القبول</li>
                    <li>يجب عليك تقديم جميع الخدمات المطلوبة أو التنسيق مع موردين آخرين</li>
                </ul>
            </div>
        </div>

        <div class="footer">
            <p class="footer-text">
                <strong>Your Events</strong><br>
                لا وسطاء، لا انتظار، ولا مكالمات ما تخلص. كل شي واضح، وسلس، وسريع
            </p>
            <p class="footer-text" style="margin-top: 20px; font-size: 12px; color: #999999;">
                © {{ date('Y') }} Your Events. جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
</body>
</html>
