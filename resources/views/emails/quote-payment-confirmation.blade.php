<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد استلام الدفع</title>
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
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            font-size: 32px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }
        .success-icon {
            font-size: 64px;
            margin-bottom: 15px;
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
        .success-box {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            border: 2px solid #10b981;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            margin: 25px 0;
        }
        .success-box h3 {
            color: #065f46;
            margin: 0 0 10px 0;
            font-size: 22px;
        }
        .success-box p {
            color: #047857;
            margin: 5px 0;
            font-size: 16px;
        }
        .payment-details {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            border: 1px solid #e5e7eb;
        }
        .payment-details h3 {
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
        .info-box {
            background-color: #dbeafe;
            border: 2px solid #3b82f6;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            text-align: right;
        }
        .info-box h4 {
            color: #1e40af;
            margin: 0 0 10px 0;
            font-size: 18px;
        }
        .info-box p {
            color: #1e3a8a;
            margin: 5px 0;
            font-size: 15px;
            line-height: 1.6;
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
            margin: 20px auto;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(91, 33, 182, 0.4);
            text-align: center;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
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
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <div class="success-icon">✅</div>
            <h1>تم استلام الدفع بنجاح!</h1>
        </div>

        <div class="content">
            <p class="greeting">عزيزي {{ $quote->user->name }},</p>

            <p class="message">
                نشكرك على إتمام عملية الدفع! تم استلام مبلغ <strong>{{ number_format($quote->total, 2) }} ر.س</strong> بنجاح.
            </p>

            <div class="success-box">
                <h3>🎉 تم تأكيد الدفع</h3>
                <p>حالة عرض السعر: <strong>تم الدفع</strong></p>
                <p>رقم المرجع: <strong>{{ $quote->quote_number }}</strong></p>
            </div>

            <div class="payment-details">
                <h3>💳 تفاصيل الدفع</h3>
                
                <div class="detail-row">
                    <span class="detail-label">رقم عرض السعر:</span>
                    <span class="detail-value">{{ $quote->quote_number }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">المبلغ المدفوع:</span>
                    <span class="detail-value">{{ number_format($quote->total, 2) }} ر.س</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">تاريخ الدفع:</span>
                    <span class="detail-value">{{ $quote->payment_date->format('Y-m-d H:i') }}</span>
                </div>
                
                @if($quote->payment_method)
                <div class="detail-row">
                    <span class="detail-label">طريقة الدفع:</span>
                    <span class="detail-value">{{ $quote->payment_method }}</span>
                </div>
                @endif

                @if($quote->payment_reference)
                <div class="detail-row">
                    <span class="detail-label">رقم العملية:</span>
                    <span class="detail-value">{{ $quote->payment_reference }}</span>
                </div>
                @endif
            </div>

            <div class="info-box">
                <h4>📢 ما التالي؟</h4>
                <p>✓ تم تحويل طلبك إلى حجز مؤكد الدفع</p>
                <p>✓ سيتم إرسال إشعارات للموردين المؤهلين</p>
                <p>✓ أول مورد يقبل الحجز سيتم تعيينه لخدمتك</p>
                <p>✓ ستتلقى إشعاراً فورياً عند قبول أحد الموردين لحجزك</p>
                <p style="margin-top: 15px; font-weight: bold;">
                    ⏰ يتنافس الموردون على حجزك لمدة 24 ساعة
                </p>
            </div>

            <div class="button-container">
                <a href="{{ url('/profile/bookings') }}" class="cta-button">
                    عرض حجوزاتي
                </a>
            </div>

            <p class="message" style="margin-top: 30px; text-align: center; color: #6b7280; font-size: 14px;">
                يمكنك متابعة حالة حجزك في أي وقت من خلال ملفك الشخصي
            </p>
        </div>

        <div class="footer">
            <p><strong>Your Events</strong></p>
            <p>شكراً لثقتك بنا!</p>
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
