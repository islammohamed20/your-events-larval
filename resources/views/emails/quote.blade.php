<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض سعر من Your Events</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            direction: rtl;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
        }
        .content {
            padding: 30px;
        }
        .panel {
            background-color: #f8f9fa;
            border-right: 4px solid #5B21B6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .panel strong {
            color: #5B21B6;
        }
        .quote-details {
            margin: 20px 0;
        }
        .quote-item {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .quote-item:last-child {
            border-bottom: none;
        }
        .total-section {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }
        .total-row.final {
            font-size: 20px;
            font-weight: bold;
            color: #5B21B6;
            border-top: 2px solid #5B21B6;
            padding-top: 15px;
            margin-top: 10px;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 100%);
            color: white !important;
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>عرض سعر جديد</h1>
            <p style="margin: 10px 0 0 0;">رقم العرض: {{ $quote->quote_number }}</p>
        </div>
        
        <div class="content">
            <p>مرحباً <strong>{{ $quote->user->name }}</strong>،</p>
            
            @if($quote->status === 'under_review')
                <p>نشكرك على تواصلك معنا. تم استلام طلب عرض السعر الخاص بك بنجاح وهو الآن <strong>قيد المراجعة</strong> من قبل فريقنا المختص.</p>
                <p>سيتم الرد عليك في أقرب وقت ممكن بعد مراجعة تفاصيل طلبك.</p>
            @elseif($quote->status === 'approved')
                <p style="color: #28a745; font-size: 18px; font-weight: bold;">
                    🎉 تهانينا! تمت الموافقة على عرض السعر الخاص بك
                </p>
                <p>يسعدنا إبلاغك بأن عرض السعر الخاص بك قد تمت الموافقة عليه. يمكنك الآن المتابعة لإتمام عملية الحجز.</p>
            @elseif($quote->status === 'rejected')
                <p style="color: #dc3545;">نأسف لإبلاغك بأنه لم يتم قبول عرض السعر في الوقت الحالي.</p>
                <p>يمكنك التواصل معنا لمزيد من التفاصيل أو لتقديم طلب جديد.</p>
            @else
                <p>نشكرك على تواصلك معنا. يسعدنا أن نقدم لك عرض السعر التالي:</p>
            @endif
            
            <div class="panel">
                <strong>تفاصيل العرض:</strong><br><br>
                <strong>رقم العرض:</strong> {{ $quote->quote_number }}<br>
                <strong>التاريخ:</strong> {{ $quote->created_at->format('Y/m/d') }}<br>
                <strong>الحالة:</strong> {{ $quote->status_text }}
            </div>
            
            <div class="quote-details">
                <h3 style="color: #5B21B6; margin-bottom: 10px;">الخدمات المطلوبة:</h3>
                <table class="services-table" dir="rtl" style="width: 100%; border-collapse: collapse; background-color: #fff; direction: rtl;">
                    <thead>
                        <tr>
                            <th style="background:#f1f3f5; padding:10px; border:1px solid #e9ecef; text-align:right;">الخدمة</th>
                            <th style="background:#f1f3f5; padding:10px; border:1px solid #e9ecef; text-align:center;">الكمية</th>
                            <th style="background:#f1f3f5; padding:10px; border:1px solid #e9ecef; text-align:center;">السعر</th>
                            <th style="background:#f1f3f5; padding:10px; border:1px solid #e9ecef; text-align:left;">المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quote->items as $item)
                        <tr>
                            <td style="padding:10px; border:1px solid #e9ecef; text-align:right;">
                                <strong>{{ $item->service_name }}</strong>
                                @if($item->service_description)
                                    <div style="color:#6c757d; font-size:12px; margin-top:4px;">{{ Str::limit($item->service_description, 90) }}</div>
                                @endif
                            </td>
                            <td style="padding:10px; border:1px solid #e9ecef; text-align:center;">{{ $item->quantity }}</td>
                            <td style="padding:10px; border:1px solid #e9ecef; text-align:center;">ريال {{ number_format($item->price, 2) }}</td>
                            <td style="padding:10px; border:1px solid #e9ecef; text-align:left;"><strong>ريال {{ number_format($item->subtotal, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="total-section">
                <div class="total-row">
                    <span>المجموع الفرعي:</span>
                    <span>{{ number_format($quote->subtotal, 2) }} ريال</span>
                </div>
                <div class="total-row">
                    <span>الضريبة (15%):</span>
                    <span>{{ number_format($quote->tax, 2) }} ريال</span>
                </div>
                @if($quote->discount > 0)
                <div class="total-row">
                    <span>الخصم:</span>
                    <span style="color: #28a745;">- {{ number_format($quote->discount, 2) }} ريال</span>
                </div>
                @endif
                <div class="total-row final">
                    <span>المجموع الإجمالي:</span>
                    <span>{{ number_format($quote->total, 2) }} ريال</span>
                </div>
            </div>
            
            @if($quote->admin_notes)
            <div class="panel">
                <strong>ملاحظات من الإدارة:</strong><br><br>
                {{ $quote->admin_notes }}
            </div>
            @endif
            
            @if($quote->status === 'approved')
            <p style="text-align: center;">
                <a href="{{ url('/quotes/' . $quote->id) }}" class="btn">
                    إتمام الحجز الآن
                </a>
            </p>
            @else
            <p style="text-align: center;">
                <a href="{{ url('/quotes/' . $quote->id) }}" class="btn">
                    عرض التفاصيل الكاملة
                </a>
            </p>
            @endif

            <!-- PDF Download Button -->
            <p style="text-align: center;">
                <a href="{{ route('quotes.download', $quote) }}" class="btn" style="background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);">
                    تحميل عرض السعر PDF
                </a>
            </p>
            
            <p>لأي استفسار إضافي أو لتأكيد الحجز، يرجى الرد على هذا البريد أو التواصل معنا عبر الموقع.</p>
        </div>
        
        <div class="footer">
            <p>شكراً لاختياركم {{ config('app.name') }}!</p>
            <p style="margin: 5px 0;">مع تحيات فريق العمل</p>
            <p style="margin: 5px 0; font-size: 12px; color: #999;">
                هذا البريد الإلكتروني تم إرساله تلقائياً، يرجى عدم الرد عليه مباشرة
            </p>
        </div>
    </div>
</body>
</html>
