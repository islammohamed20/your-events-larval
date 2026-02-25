<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قبول عرض السعر من المورد</title>
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
        .supplier-info {
            background: linear-gradient(135deg, #f8f4ff 0%, #fff 100%);
            border-right: 4px solid #7C3AED;
            padding: 20px;
            margin: 20px 0;
            border-radius: 10px;
        }
        .supplier-info h3 {
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 تم قبول عرض السعر!</h1>
        </div>
        
        <div class="content">
            <p>عزيزي/عزيزتي <strong>{{ $quote->user->name }}</strong>،</p>
            
            <p>يسعدنا إبلاغك بأن عرض السعر الخاص بك قد تم قبوله من قبل أحد موردينا المميزين!</p>
            
            <div class="info-box">
                <h3>📋 معلومات عرض السعر</h3>
                <table>
                    <tr>
                        <td>رقم العرض:</td>
                        <td><strong>{{ $quote->quote_number }}</strong></td>
                    </tr>
                    <tr>
                        <td>الإجمالي:</td>
                        <td><strong>{{ number_format($quote->total, 2) }} {{ __('common.currency') }}</strong></td>
                    </tr>
                    <tr>
                        <td>تاريخ القبول:</td>
                        <td>{{ optional($quote->supplier_accepted_at)->format('Y-m-d H:i') }}</td>
                    </tr>
                </table>
            </div>

            <div class="supplier-info">
                <h3>👤 معلومات المورد</h3>
                <table>
                    <tr>
                        <td>اسم المورد:</td>
                        <td><strong>{{ $supplier->name }}</strong></td>
                    </tr>
                    <tr>
                        <td>البريد الإلكتروني:</td>
                        <td><a href="mailto:{{ $supplier->email }}">{{ $supplier->email }}</a></td>
                    </tr>
                    <tr>
                        <td>رقم الهاتف:</td>
                        <td><a href="tel:{{ $supplier->primary_phone }}">{{ $supplier->primary_phone }}</a></td>
                    </tr>
                    @if($supplier->secondary_phone)
                    <tr>
                        <td>هاتف إضافي:</td>
                        <td><a href="tel:{{ $supplier->secondary_phone }}">{{ $supplier->secondary_phone }}</a></td>
                    </tr>
                    @endif
                </table>
            </div>

            @if($quote->supplier_notes)
            <div class="info-box">
                <h3>📝 ملاحظات المورد</h3>
                <p>{{ $quote->supplier_notes }}</p>
            </div>
            @endif

            <p><strong>الخطوة التالية:</strong></p>
            <p>يرجى استكمال بيانات الحجز والدفع لإتمام العملية.</p>
            
            <div style="text-align: center; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <a href="{{ url('/quotes/' . $quote->id) }}" class="button" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">عرض تفاصيل العرض</a>
                <a href="{{ url('/quotes/' . $quote->id . '/payment') }}" class="button" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">استكمال بيانات الحجز والدفع</a>
            </div>
            
            <p style="margin-top: 30px; color: #666; font-size: 14px;">
                إذا كانت لديك أي استفسارات، لا تتردد في التواصل معنا.
            </p>
        </div>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} Your Events. جميع الحقوق محفوظة.</p>
            <p>هذا البريد الإلكتروني تم إرساله تلقائياً، يرجى عدم الرد عليه.</p>
        </div>
    </div>
</body>
</html>
