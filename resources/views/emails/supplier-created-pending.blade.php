<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم إنشاء حساب مورد</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background: #f6f7fb;
            margin: 0;
            padding: 24px;
            color: #1f2937;
        }
        .mail-wrap {
            max-width: 680px;
            margin: 0 auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
        }
        .head {
            background: #0f766e;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }
        .body {
            padding: 22px;
            line-height: 1.9;
            font-size: 15px;
            text-align: center;
        }
        .note {
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            color: #115e59;
            border-radius: 8px;
            padding: 12px;
            margin: 14px 0;
        }
        .box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            margin: 14px 0;
        }
        .btn {
            display: inline-block;
            margin-top: 10px;
            background: #0f766e;
            color: #ffffff !important;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 8px;
            text-align: center;
        }
        .foot {
            padding: 14px 22px 20px;
            color: #6b7280;
            font-size: 13px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="mail-wrap">
        <div class="head">
            <h2 style="margin:0;">تم إنشاء حساب مورد</h2>
        </div>

        <div class="body">
            <p>مرحباً {{ $supplierName ?? 'عزيزي المورد' }}،</p>

            <p>تم إنشاء حسابك كمورد لدى {{ $companyName ?? 'Your Events' }} من قبل إدارة المنصة.</p>

            <div class="note">
                حالة الحساب الحالية: <strong>قيد المراجعة</strong>.
                سيتم إشعارك برسالة جديدة فور اعتماد الحساب.
            </div>

            <div class="box">
                <div><strong>البريد الإلكتروني:</strong> {{ $supplierEmail ?? '-' }}</div>
                <div><strong>تاريخ الإنشاء:</strong> {{ $createdAt ?? now()->format('Y/m/d H:i') }}</div>
            </div>

            @if(!empty($supplierLoginUrl))
                <a class="btn" href="{{ $supplierLoginUrl }}">الدخول إلى بوابة المورد</a>
            @endif
        </div>

        <div class="foot">
            للدعم أو الاستفسارات: {{ $supportEmail ?? 'hello@yourevents.sa' }}
        </div>
    </div>
</body>
</html>