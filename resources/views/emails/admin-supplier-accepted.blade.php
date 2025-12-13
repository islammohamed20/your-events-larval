<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قبول مورد لعرض سعر</title>
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
        .content {
            padding: 30px;
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
            padding: 12px 30px;
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 100%);
            color: #ffffff;
            text-decoration: none;
            border-radius: 25px;
            margin: 20px 0;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(91, 33, 182, 0.3);
        }
        .button:hover {
            background: linear-gradient(135deg, #7C3AED 0%, #A855F7 100%);
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ مورد قبل عرض سعر</h1>
        </div>
        
        <div class="content">
            <p>مرحباً فريق الإدارة،</p>
            
            <p>تم قبول عرض السعر التالي من قبل أحد الموردين:</p>
            
            <div class="info-box">
                <h3>📋 معلومات عرض السعر</h3>
                <table>
                    <tr>
                        <td>رقم العرض:</td>
                        <td><strong>{{ $quote->quote_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>العميل:</td>
                        <td><strong>{{ $quote->user->name }}</strong></td>
                    </tr>
                    <tr>
                        <td>بريد العميل:</td>
                        <td>{{ $quote->user->email }}</td>
                    </tr>
                    <tr>
                        <td>الإجمالي:</td>
                        <td><strong>{{ number_format($quote->total, 2) }} ر.س</strong></td>
                    </tr>
                </table>
            </div>

            <div class="info-box">
                <h3>👤 المورد الذي قبل العرض</h3>
                <table>
                    <tr>
                        <td>اسم المورد:</td>
                        <td><strong>{{ $supplier->name }}</strong></td>
                    </tr>
                    <tr>
                        <td>البريد الإلكتروني:</td>
                        <td>{{ $supplier->email }}</td>
                    </tr>
                    <tr>
                        <td>رقم الهاتف:</td>
                        <td>{{ $supplier->primary_phone }}</td>
                    </tr>
                    <tr>
                        <td>تاريخ القبول:</td>
                        <td>{{ optional($quote->supplier_accepted_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </div>

            @if($quote->supplier_notes)
            <div class="info-box">
                <h3>📝 ملاحظات المورد</h3>
                <p>{{ $quote->supplier_notes }}</p>
            </div>
            @endif
            
            <div style="text-align: center;">
                <a href="{{ url('/admin/quotes/' . $quote->id) }}" class="button">عرض التفاصيل في لوحة التحكم</a>
            </div>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Events. جميع الحقوق محفوظة.</p>
            <p>هذا البريد الإلكتروني تم إرساله تلقائياً.</p>
        </div>
    </div>
</body>
</html>
