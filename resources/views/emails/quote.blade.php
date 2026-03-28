<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض سعر من Your Events</title>
    <style>
        .body {
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            direction: rtl;
            text-align: center;
        }
        .container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            text-align: center;
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
            margin-bottom: 10px;
        }
        .header .quote-number {
            background: rgba(255,255,255,0.2);
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 10px;
        }
        .content {
            padding: 35px;
            line-height: 1.8;
            text-align: center;
        }
        .greeting {
            font-size: 20px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }
        .intro-text {
            color: #555;
            font-size: 16px;
            margin-bottom: 25px;
            background: linear-gradient(135deg, #f8f4ff 0%, #fff 100%);
            padding: 20px;
            border-radius: 10px;
            border-right: 4px solid #7C3AED;
            text-align: center;
        }
        .section-title {
            color: #5B21B6;
            font-size: 18px;
            font-weight: bold;
            margin: 30px 0 15px 0;
            display: flex;
            align-items: center;
        }
        .section-title::before {
            content: "📋";
            margin-left: 10px;
        }
        .panel {
            background-color: #f8f9fa;
            border-right: 4px solid #5B21B6;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            text-align: center;
        }
        .panel strong {
            color: #5B21B6;
        }
        .quote-details {
            margin: 25px 0;
            text-align: center;
        }
        .services-table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .services-table th {
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 100%);
            color: white;
            padding: 15px;
            font-weight: 600;
        }
        .services-table td {
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .services-table tr:last-child td {
            border-bottom: none;
        }
        .total-section {
            background: linear-gradient(135deg, #f8f4ff 0%, #fff 100%);
            padding: 25px;
            margin: 25px 0;
            border-radius: 12px;
            border: 2px solid #e9e3ff;
            text-align: center;
        }
        .total-row {
            display: flex;
            justify-content: center;
            padding: 12px 0;
            font-size: 15px;
            text-align: center;
            flex-wrap: wrap;
        }
        .total-row.final {
            font-size: 22px;
            font-weight: bold;
            color: #5B21B6;
            border-top: 2px solid #5B21B6;
            padding-top: 18px;
            margin-top: 12px;
        }
        .why-us {
            background: #fff;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border: 1px solid #e9ecef;
            text-align: center;
        }
        .why-us h3 {
            color: #5B21B6;
            margin-bottom: 20px;
            font-size: 20px;
            text-align: center;
        }
        .why-us-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
            text-align: right;
        }
        .why-us-item .icon {
            font-size: 20px;
            margin-left: 12px;
            min-width: 30px;
        }
        .why-us-item .text {
            color: #555;
            font-size: 14px;
            line-height: 1.6;
            text-align: right;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #5B21B6 0%, #7C3AED 100%);
            color: white !important;
            padding: 16px 45px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 16px;
            margin: 10px 5px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(91, 33, 182, 0.3);
            transition: all 0.3s ease;
        }
        .btn-secondary {
            background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }
        .btn-container {
            text-align: center;
            margin: 30px 0;
        }
        .next-step {
            background: linear-gradient(135deg, #fef3c7 0%, #fff 100%);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .next-step h4 {
            color: #d97706;
            margin: 0 0 15px 0;
            font-size: 18px;
        }
        .footer {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
            text-align: center;
            padding: 30px;
            color: #fff;
        }
        .footer .brand {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #A855F7;
        }
        .footer .tagline {
            color: #d1d5db;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .footer .contact-info {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            margin-top: 15px;
        }
        .footer .contact-item {
            color: #d1d5db;
            font-size: 13px;
        }
        .footer .contact-item a {
            color: #A855F7;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background: linear-gradient(to left, transparent, #e9ecef, transparent);
            margin: 25px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ $message->embed(public_path('images/logo/logo_mail.png')) }}" alt="Your Events" style="max-width: 180px; height: auto; margin-bottom: 20px;">
            <h1>🎉 جاهز لفعاليتك؟</h1>
            <p style="margin: 10px 0 0 0; font-size: 16px; line-height: 1.6;">لا وسطاء، لا انتظار، ولا مكالمات ما تخلص. كل شي واضح، وسلس، وسريع</p>
            <div class="quote-number">رقم العرض: {{ $quote->quote_number }}</div>
        </div>
        
        <div class="content">
            <p class="greeting">هلا <strong>{{ $quote->user->name }}</strong>! 👋</p>
            
            <div class="intro-text">
                اللحظة اللي تنتظرها وصلت… عرضك الخاص من <strong>Your Events</strong> صار جاهز!<br><br>
                إحنا مو بس نرسل لك أسعار وخدمات… إحنا نحط لك تجربة كاملة، خطوة بخطوة، بحيث كل شيء مرتب، من البداية للنهاية، وانت بس استمتع. 🎊
            </div>
            
            <div class="panel">
                <strong>رقم العرض:</strong> {{ $quote->quote_number }}<br>
                <strong>التاريخ:</strong> {{ $quote->created_at->format('Y/m/d') }}<br>
                <strong>الحالة:</strong> {{ $quote->status_text }}
            </div>
            
            <div class="quote-details">
                <h3 style="color: #5B21B6; margin-bottom: 15px;">📦 جدول الكميات والأسعار:</h3>
                <table class="services-table" dir="rtl">
                    <thead>
                        <tr>
                            <th style="text-align:right; border-radius: 0 8px 0 0;">الخدمة</th>
                            <th style="text-align:center;">الكمية</th>
                            <th style="text-align:center;">السعر</th>
                            <th style="text-align:left; border-radius: 8px 0 0 0;">المجموع</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quote->items as $item)
                        <tr>
                            <td style="text-align:right;">
                                <strong>{{ $item->service->name ?? $item->service_name }}</strong>
                                @php
                                    $desc = $item->service->description ?? $item->service_description;
                                @endphp
                                @if($desc)
                                    <div style="color:#6c757d; font-size:12px; margin-top:4px;">{{ Str::limit($desc, 90) }}</div>
                                @endif
                            </td>
                            <td style="text-align:center;">{{ $item->quantity }}</td>
                            <td style="text-align:center;">{{ number_format($item->price, 2) }} {{ __('common.currency') }}</td>
                            <td style="text-align:left;"><strong>{{ number_format($item->subtotal, 2) }} {{ __('common.currency') }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="total-section">
                <div class="total-row">
                    <span>المجموع الفرعي:</span>
                    <span>{{ number_format($quote->subtotal, 2) }} {{ __('common.currency') }}</span>
                </div>
                <div class="total-row">
                    <span>الضريبة (15%):</span>
                    <span>{{ number_format($quote->tax, 2) }} {{ __('common.currency') }}</span>
                </div>
                @if($quote->discount > 0)
                <div class="total-row">
                    <span>الخصم:</span>
                    <span style="color: #28a745;">- {{ number_format($quote->discount, 2) }} {{ __('common.currency') }}</span>
                </div>
                @endif
                <div class="total-row final">
                    <span>💰 المجموع الإجمالي:</span>
                    <span>{{ number_format($quote->total, 2) }} {{ __('common.currency') }}</span>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Why Your Events Section -->
            <div class="why-us">
                <h3>✨ ليش Your Events؟</h3>
                
                <div class="why-us-item">
                    <span class="icon">🌟</span>
                    <span class="text"><strong>نفكر في كل شيء قبلك:</strong> من المعدات لتنسيق المكان للأنشطة المرحة، كل شيء مجهز عشان فعاليتك تصير على أتم وجه.</span>
                </div>
                
                <div class="why-us-item">
                    <span class="icon">🌟</span>
                    <span class="text"><strong>مرونة غير محدودة:</strong> صغيرة، كبيرة، 20 أو 200 شخص؟ إحنا نقدر نضبط كل شي حسب احتياجك بدون تعب أو قلق.</span>
                </div>
                
                <div class="why-us-item">
                    <span class="icon">🌟</span>
                    <span class="text"><strong>نكهة سعودية مميزة:</strong> فعالياتنا فيها روح مرحة وخفيفة… نضمن لكل فريق ضحكة وذكريات حلوة.</span>
                </div>
                
                <div class="why-us-item">
                    <span class="icon">🌟</span>
                    <span class="text"><strong>تجربة سلسة ومريحة:</strong> كل التفاصيل واضحة والأسعار شفافة، بدون مفاجآت… بس تأكيد منك وتكون جاهز للمرح!</span>
                </div>
                
                <div class="why-us-item">
                    <span class="icon">🌟</span>
                    <span class="text"><strong>التفاصيل تصنع الفرق:</strong> كل أداة، كل نشاط، كل فكرة… مصممة بعناية لتخلي فعاليتك حديث كل من حضرها.</span>
                </div>
            </div>

            @if($quote->admin_notes)
            <div class="panel">
                <strong>📝 ملاحظات من الإدارة:</strong><br><br>
                {{ $quote->admin_notes }}
            </div>
            @endif

            <!-- Next Step Section -->
            <div class="next-step">
                <h4>🚀 الخطوة الجاية</h4>
                <p style="color: #555; margin: 0;">حمّل عرض السعر أو أكد طلبك الحين!</p>
            </div>

            <div class="btn-container">
                <a href="{{ route('quotes.show', $quote) }}" class="btn">
                    📋 تفاصيل عرضك
                </a>
                @if($quote->status === 'approved' && $quote->payment_status !== 'paid')
                <a href="{{ route('quotes.complete-booking', $quote) }}" class="btn btn-secondary">
                    ✅ استكمال بيانات الحجز والدفع
                </a>
                @endif
                <a href="{{ route('quotes.download', $quote) }}" class="btn btn-secondary">
                    ⬇️ تحميل عرض السعر PDF
                </a>
            </div>

            <div class="divider"></div>
            
            <p style="text-align: center; color: #7C3AED; font-size: 18px; font-weight: bold;">
                مع Your Events… كل لحظة تستحق التميز 🌟
            </p>
        </div>
        
        <div class="footer" style="direction: rtl; text-align: center;">
            <div class="brand" style="text-align: center;">Your Events</div>
            <div class="tagline" style="text-align: center;">لا وسطاء، لا انتظار، ولا مكالمات ما تخلص. كل شي واضح، وسلس، وسريع</div>
            
            <div class="contact-info" style="text-align: center; display: flex; justify-content: center; align-items: center; gap: 15px; flex-wrap: wrap;">
                <span class="contact-item">✉️ <a href="mailto:hello@yourevents.sa">hello@yourevents.sa</a></span>
                <span style="color: #d1d5db;">|</span>
                <span class="contact-item">📞 <span dir="ltr" style="unicode-bidi: bidi-override; direction: ltr;">+966 50 515 9616</span></span>
                <span style="color: #d1d5db;">|</span>
                <span class="contact-item">🌐 <a href="{{ config('app.url') }}">{{ config('app.url') }}</a></span>
            </div>
            
            <p style="margin-top: 20px; font-size: 11px; color: #9ca3af; text-align: center;">
                هذا البريد الإلكتروني تم إرساله تلقائياً من نظام Your Events
            </p>
        </div>
    </div>
</body>
</html>
