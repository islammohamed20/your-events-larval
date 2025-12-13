<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب جديد - Your Events</title>
    <style>
        body {
            font-family: 'Tajawal', Arial, sans-serif;
            direction: rtl;
            text-align: right;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 650px;
            margin: 20px auto;
            background-color: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .header {
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 50%, #A855F7 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .content {
            padding: 30px 20px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #5B21B6;
            margin: 30px 0 15px 0;
            border-bottom: 2px solid #7C3AED;
            padding-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .detail-label {
            font-weight: bold;
            color: #333;
            width: 40%;
        }
        .detail-value {
            color: #666;
            width: 60%;
            text-align: left;
        }
        .note {
            background-color: #f0f7ff;
            border-right: 4px solid #2dbcae;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
        }
        .button-section {
            text-align: center;
            margin: 30px 0;
        }
        .accept-btn {
            display: inline-block;
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 100%);
            color: white;
            padding: 15px 40px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(91, 33, 182, 0.3);
        }
        .accept-btn:hover {
            background: linear-gradient(135deg, #7C3AED 0%, #A855F7 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 72, 112, 0.3);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
            border-top: 1px solid #ddd;
        }
        .footer p {
            margin: 5px 0;
        }
        .divider {
            margin: 20px 0;
            border: none;
            border-top: 1px solid #ddd;
        }
        .urgency-badge {
            display: inline-block;
            background-color: #fff3cd;
            color: #856404;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>🎉 طلب جديد</h1>
            <p>لديك طلب جديد بانتظار استجابتك</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p>السلام عليكم ورحمة الله وبركاته {{ $supplier->name }},</p>

            <p>تم تلقي طلب جديد يتطابق مع الخدمات التي تقدمها. تفاصيل الطلب أدناه:</p>

            <div class="urgency-badge">⚡ هذا الطلب متاح الآن - كن الأول لقبوله!</div>

            <!-- Order Details Section -->
            <div class="section">
                <div class="section-title">📋 تفاصيل الطلب</div>
                
                <div class="detail-row">
                    <span class="detail-label">رقم الطلب:</span>
                    <span class="detail-value">#{{ $order->id }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">الخدمة:</span>
                    <span class="detail-value">{{ $order->service->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">الفئة:</span>
                    <span class="detail-value">{{ $order->category->name }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">الكمية:</span>
                    <span class="detail-value">{{ $order->quantity }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">السعر المقترح:</span>
                    <span class="detail-value">{{ number_format($order->price, 2) }} ريال</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">تاريخ الطلب:</span>
                    <span class="detail-value">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            <!-- Customer Notes -->
            @if($order->customer_notes)
            <div class="section">
                <div class="section-title">📝 ملاحظات العميل</div>
                <div class="note">
                    {{ $order->customer_notes }}
                </div>
            </div>
            @endif

            <!-- General Notes -->
            @if($order->general_notes)
            <div class="section">
                <div class="section-title">📌 ملاحظات عامة</div>
                <div class="note">
                    {{ $order->general_notes }}
                </div>
            </div>
            @endif

            <!-- Call to Action -->
            <div class="button-section">
                <p style="color: #666; margin-bottom: 20px;">اضغط الزر أدناه لقبول هذا العرض</p>
                <a href="{{ url('/api/orders/' . $order->id . '/accept?supplier_id=' . $supplier->id) }}" class="accept-btn">
                    ✓ قبول عرض السعر
                </a>
            </div>

            <div class="divider"></div>

            <!-- Additional Info -->
            <div class="section" style="background-color: #f0f7ff; padding: 15px; border-radius: 5px;">
                <p style="margin: 0; color: #333; font-size: 14px;">
                    <strong>ملاحظة مهمة:</strong> هذا الطلب متاح للموردين الآخرين أيضاً. أول مورد يقبل هذا العرض سيحصل على الطلب.
                </p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><strong>Your Events - محرك أحداثك</strong></p>
            <p>جميع الحقوق محفوظة © 2025</p>
            <p>إذا لم تكن ترغب في تلقي هذه الرسائل، يمكنك تحديث تفضيلاتك من صفحة حسابك.</p>
        </div>
    </div>
</body>
</html>
