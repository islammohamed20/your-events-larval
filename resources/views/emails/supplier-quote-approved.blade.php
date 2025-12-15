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
            margin: 0;
            font-size: 28px;
            margin-bottom: 10px;
        }
        .urgent-badge {
            background-color: #ffd700;
            color: #000;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .content {
            padding: 30px;
        }
        .highlight-box {
            background: linear-gradient(135deg, #f8f4ff 0%, #fff 100%);
            border-right: 5px solid #7C3AED;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 500;
            text-align: center;
        }
        .info-box {
            background-color: #f8f9fa;
            border-right: 4px solid #5B21B6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #5B21B6;
        }
        .button {
            display: inline-block;
            padding: 15px 40px;
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: bold;
            font-size: 18px;
            box-shadow: 0 4px 15px rgba(91, 33, 182, 0.3);
        }
        .button:hover {
            background: linear-gradient(135deg, #7C3AED 0%, #A855F7 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(91, 33, 182, 0.4);
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        table td:first-child {
            font-weight: bold;
            color: #666;
            width: 40%;
        }
        .warning-text {
            color: #d32f2f;
            font-weight: bold;
            font-size: 16px;
            text-align: center;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔥 فرصة عمل جديدة!</h1>
            <span class="urgent-badge">⚡ عاجل - التنافس الآن!</span>
        </div>
        
        <div class="content">
            <p>عزيزي/عزيزتي <strong>{{ $supplier->name }}</strong>،</p>
            
            <div class="highlight-box">
                🎯 تم الموافقة على عرض سعر يحتوي على خدماتك!<br>
                <strong>أول مورد يقبل العرض سيفوز بالعميل!</strong>
            </div>
            
            <div class="info-box">
                <h3>📋 تفاصيل عرض السعر</h3>
                <table>
                    <tr>
                        <td>رقم العرض:</td>
                        <td><strong>{{ $quote->quote_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>الإجمالي الكلي:</td>
                        <td><strong style="color: #4caf50; font-size: 18px;">{{ number_format($quote->total, 2) }} ر.س</strong></td>
                    </tr>
                    <tr>
                        <td>تاريخ الموافقة:</td>
                        <td>{{ optional($quote->approved_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="info-box">
                <h3>👤 معلومات العميل</h3>
                <table>
                    <tr>
                        <td>اسم العميل:</td>
                        <td><strong>{{ $quote->user->name }}</strong></td>
                    </tr>
                    <tr>
                        <td>عدد العناصر:</td>
                        <td>{{ $quote->items->count() }}</td>
                    </tr>
                </table>
            </div>

            <p class="warning-text">
                ⏰ لا تفوت هذه الفرصة! الموردون الآخرون يشاهدون هذا العرض الآن.
            </p>
            
            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ url('/supplier/quotes/' . $quote->id) }}" class="button">
                    <i class="fas fa-bolt"></i> <span style="color: white;">عرض التفاصيل وقبول العرض</span>
                </a>
            </div>

            <p style="text-align: center; color: #666; font-size: 14px; margin-top: 20px;">
                يمكنك مراجعة جميع تفاصيل العرض وقبوله مباشرة من لوحة تحكم المورد.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Events. جميع الحقوق محفوظة.</p>
            <p>هذا البريد الإلكتروني تم إرساله تلقائياً، يرجى عدم الرد عليه.</p>
        </div>
    </div>
</body>
</html>
