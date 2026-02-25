<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز جديد متاح</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 40px 20px;
            direction: rtl;
            text-align: right;
        }
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        .header {
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 50%, #A855F7 100%);
            padding: 40px 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: pulse 3s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        .header h1 {
            color: #ffffff;
            font-size: 32px;
            margin: 0 0 10px 0;
            font-weight: bold;
            position: relative;
            z-index: 1;
        }
        .urgent-badge {
            display: inline-block;
            background-color: #ef4444;
            color: white;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 10px;
            animation: blink 1.5s infinite;
            position: relative;
            z-index: 1;
        }
        @keyframes blink {
            0%, 50%, 100% { opacity: 1; }
            25%, 75% { opacity: 0.7; }
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: right;
        }
        .message {
            color: #4b5563;
            line-height: 1.8;
            margin-bottom: 30px;
            font-size: 16px;
            text-align: right;
        }
        .countdown-box {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
        }
        .countdown-box h3 {
            color: #92400e;
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .countdown-timer {
            font-size: 32px;
            font-weight: bold;
            color: #dc2626;
            direction: ltr;
        }
        .booking-details {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            border: 1px solid #e5e7eb;
        }
        .booking-details h3 {
            color: #5B21B6;
            margin: 0 0 20px 0;
            font-size: 20px;
            text-align: right;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e5e7eb;
            text-align: right;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 600;
            color: #6b7280;
            text-align: right;
        }
        .detail-value {
            color: #1f2937;
            font-weight: 500;
            text-align: left;
        }
        .services-list {
            background-color: #ffffff;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }
        .service-item {
            padding: 10px;
            background-color: #f3f4f6;
            border-radius: 6px;
            margin-bottom: 8px;
            text-align: right;
        }
        .service-item:last-child {
            margin-bottom: 0;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 100%);
            color: #ffffff !important;
            text-decoration: none;
            padding: 16px 40px;
            border-radius: 50px;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 10px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(91, 33, 182, 0.4);
            text-align: center;
        }
        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(91, 33, 182, 0.6);
        }
        .reject-button {
            background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
            box-shadow: 0 4px 15px rgba(107, 114, 128, 0.4);
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .warning-box {
            background-color: #fef2f2;
            border: 2px solid #fca5a5;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            text-align: right;
        }
        .warning-box p {
            color: #991b1b;
            margin: 0;
            font-weight: 600;
            font-size: 15px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            color: #6b7280;
            margin: 5px 0;
            font-size: 14px;
        }
        .footer a {
            color: #5B21B6;
            text-decoration: none;
            font-weight: 600;
        }
        @media only screen and (max-width: 600px) {
            .email-container {
                border-radius: 0;
            }
            .header h1 {
                font-size: 24px;
            }
            .content {
                padding: 25px 20px;
            }
            .cta-button {
                display: block;
                margin: 10px 0;
                padding: 14px 30px;
                font-size: 16px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>🔥 حجز جديد متاح للتنافس!</h1>
            <span class="urgent-badge">⏰ عاجل - التنافس مفتوح الآن</span>
        </div>

        <div class="content">
            <p class="greeting">مرحباً {{ $supplier->company_name }},</p>

            <p class="message">
                يسعدنا إبلاغك بوجود حجز جديد متاح للتنافس عليه!<br>
                <strong>أول مورد يقبل الحجز سيحصل عليه.</strong><br>
                الرجاء المراجعة واتخاذ القرار بسرعة.
            </p>

            <div class="countdown-box">
                <h3>⏳ الوقت المتبقي للتنافس</h3>
                <div class="countdown-timer">
                    {{ $booking->expires_at->diffForHumans(['parts' => 2]) }}
                </div>
                <p style="color: #92400e; margin: 10px 0 0 0; font-size: 14px;">
                    ينتهي في: {{ $booking->expires_at->format('Y-m-d H:i') }}
                </p>
            </div>

            <div class="booking-details">
                <h3>📋 تفاصيل الحجز</h3>
                
                <div class="detail-row">
                    <span class="detail-label">رقم المرجع:</span>
                    <span class="detail-value">{{ $booking->booking_reference }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">اسم العميل:</span>
                    <span class="detail-value">{{ $booking->client_name }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">المبلغ الإجمالي:</span>
                    <span class="detail-value">{{ number_format($booking->total_amount, 2) }} {{ __('common.currency') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">حالة الدفع:</span>
                    <span class="detail-value" style="color: #16a34a; font-weight: bold;">✓ تم الدفع</span>
                </div>

                @if($booking->quote && $booking->quote->items->count() > 0)
                <div style="margin-top: 20px;">
                    <span class="detail-label" style="display: block; margin-bottom: 10px;">الخدمات المطلوبة:</span>
                    <div class="services-list">
                        @foreach($booking->quote->items as $item)
                        <div class="service-item">
                            <strong>{{ $item->service->name ?? $item->service_name }}</strong>
                            @if($item->quantity > 1)
                            <span style="color: #6b7280;"> (الكمية: {{ $item->quantity }})</span>
                            @endif
                            <br>
                            <span style="color: #9ca3af; font-size: 14px;">{{ number_format($item->price, 2) }} {{ __('common.currency') }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <div class="warning-box">
                <p>⚠️ تنبيه: هذا الحجز متاح لجميع الموردين المؤهلين. أول من يقبل سيحصل على الحجز!</p>
            </div>

            <div class="button-container">
                <a href="{{ url('/supplier/bookings/' . $booking->id) }}" class="cta-button">
                    ✅ <span style="color: white;">عرض التفاصيل والقبول</span>
                </a>
                <a href="{{ url('/supplier/bookings/' . $booking->id) }}" class="cta-button reject-button">
                    ❌ <span style="color: white;">عرض التفاصيل والرفض</span>
                </a>
            </div>

            <p class="message" style="margin-top: 30px; font-size: 14px; color: #6b7280; text-align: center;">
                لديك {{ $booking->expires_at->diffInHours() }} ساعة للرد على هذا الحجز قبل انتهاء الصلاحية.
            </p>
        </div>

        <div class="footer">
            <p><strong>Your Events</strong></p>
            <p>لأي استفسارات، يرجى التواصل معنا على:</p>
            <p>
                البريد الإلكتروني: <a href="mailto:support@your-events.com">support@your-events.com</a><br>
                الهاتف: <a href="tel:+966123456789">+966 12 345 6789</a>
            </p>
            <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">
                &copy; {{ date('Y') }} Your Events. جميع الحقوق محفوظة.
            </p>
        </div>
    </div>
</body>
</html>
