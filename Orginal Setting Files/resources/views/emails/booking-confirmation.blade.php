<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد طلب الحجز - Your Events</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            direction: rtl;
            text-align: right;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #D4AF37, #EFD469);
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .booking-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            color: #333;
        }
        .status-badge {
            background-color: #ffc107;
            color: #000;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .contact-info {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .footer {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 14px;
        }
        .btn {
            display: inline-block;
            background-color: #D4AF37;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>تأكيد طلب الحجز</h1>
            <p>Your Events - نحول مناسبتك العادية إلى لحظة استثنائية</p>
        </div>

        <!-- Content -->
        <div class="content">
            <h2>عزيزي {{ $booking->client_name }}،</h2>
            
            <p>شكراً لك على الثقة بخدماتنا! تم استلام طلب الحجز الخاص بك بنجاح.</p>
            
            <div class="booking-details">
                <h3 style="color: #D4AF37; margin-top: 0;">تفاصيل الحجز:</h3>
                
                <div class="detail-row">
                    <span class="detail-label">رقم الحجز:</span>
                    <span class="detail-value">{{ $booking->booking_reference }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">تاريخ الطلب:</span>
                    <span class="detail-value">{{ $booking->created_at->format('d/m/Y H:i') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">تاريخ المناسبة:</span>
                    <span class="detail-value">{{ \Carbon\Carbon::parse($booking->event_date)->format('d/m/Y') }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">مكان المناسبة:</span>
                    <span class="detail-value">{{ $booking->event_location }}</span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">عدد الضيوف:</span>
                    <span class="detail-value">{{ number_format($booking->guests_count) }}</span>
                </div>
                
                @if($booking->package)
                <div class="detail-row">
                    <span class="detail-label">الباقة المحجوزة:</span>
                    <span class="detail-value">{{ $booking->package->name }}</span>
                </div>
                @endif
                
                @if($booking->service)
                <div class="detail-row">
                    <span class="detail-label">الخدمة المحجوزة:</span>
                    <span class="detail-value">{{ $booking->service->name }}</span>
                </div>
                @endif
                
                @if($booking->total_amount > 0)
                <div class="detail-row">
                    <span class="detail-label">التكلفة المقدرة:</span>
                    <span class="detail-value">{{ number_format($booking->total_amount) }} ر.س</span>
                </div>
                @endif
                
                <div class="detail-row">
                    <span class="detail-label">حالة الحجز:</span>
                    <span class="detail-value"><span class="status-badge">قيد المراجعة</span></span>
                </div>
            </div>

            @if($booking->special_requests)
            <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;">
                <h4 style="color: #856404; margin-top: 0;">الطلبات الخاصة:</h4>
                <p style="margin: 0; color: #856404;">{{ $booking->special_requests }}</p>
            </div>
            @endif

            <h3>الخطوات التالية:</h3>
            <ol>
                <li>سيقوم فريقنا بمراجعة طلبك خلال 24 ساعة</li>
                <li>سنتواصل معك هاتفياً لتأكيد التفاصيل</li>
                <li>بعد التأكيد، سنبدأ في التحضير لمناسبتك</li>
            </ol>

            <div class="contact-info">
                <h4 style="color: #1976d2; margin-top: 0;">معلومات الاتصال:</h4>
                <p><strong>الهاتف:</strong> +966 50 123 4567</p>
                <p><strong>البريد الإلكتروني:</strong> info@yourevents.com</p>
                <p><strong>ساعات العمل:</strong> السبت - الخميس: 9:00 ص - 6:00 م</p>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <p>هل لديك استفسار؟ لا تتردد في التواصل معنا</p>
                <a href="tel:+966501234567" class="btn">اتصل بنا الآن</a>
            </div>

            <p>شكراً لاختيارك Your Events لتنظيم مناسبتك. نتطلع لجعل مناسبتك لا تُنسى!</p>

            <p>مع أطيب التحيات،<br>
            <strong>فريق Your Events</strong></p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Events. جميع الحقوق محفوظة.</p>
            <p>الرياض، المملكة العربية السعودية</p>
        </div>
    </div>
</body>
</html>
