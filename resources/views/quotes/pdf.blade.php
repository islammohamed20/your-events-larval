<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>عرض السعر #{{ $quote->quote_number }}</title>
    <style>
        @page {
            margin: 20mm 15mm 30mm 15mm;
        }
        
        body {
            font-family: 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 12px;
            line-height: 1.6;
            color: #333;
        }
        
        .container {
            width: 100%;
            padding: 10px;
        }
        
        .header {
            display: none; /* Hidden since letter head provides header */
        }
        
        .quote-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1f144a;
            margin: 80px 0 20px 0; /* Space for letter head header */
            padding: 10px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        
        .info-table td {
            padding: 10px 8px;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        .info-label {
            background: #f5f5f5;
            font-weight: bold;
            width: 35%;
        }
        
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-approved { background: #d4edda; color: #155724; }
        .status-rejected { background: #f8d7da; color: #721c24; }
        .status-completed { background: #d1ecf1; color: #0c5460; }
        .status-paid { background: #cce5ff; color: #004085; }
        
        h3 {
            color: #1f144a;
            margin: 10px 0 8px 0;
            font-size: 14px;
        }
        
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            background: #fff;
        }
        
        table.items th {
            background: #f7f7f7;
            padding: 8px 10px;
            text-align: right;
            font-weight: bold;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        table.items td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 11px;
            background: #fff;
            vertical-align: top;
        }
        
        table.items tr:nth-child(even) td {
            background: #fafafa;
        }
        
        .service-name {
            font-weight: bold;
            color: #1f144a;
            margin-bottom: 3px;
        }
        
        .service-description {
            font-size: 10px;
            color: #666;
            margin-top: 4px;
            line-height: 1.5;
        }
        
        .service-notes {
            font-size: 9px;
            color: #0066cc;
            margin-top: 3px;
            font-style: italic;
        }
        
        .summary-table {
            width: 60%;
            margin-left: auto;
            margin-right: 0;
            border-collapse: collapse;
            margin-top: 0;
            border: 2px solid #1f144a;
        }
        
        .summary-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        
        .summary-label {
            font-weight: bold;
            text-align: right;
            width: 60%;
        }
        
        .summary-value {
            text-align: left;
            font-weight: bold;
        }
        
        .total-row {
            background: #f2f2f7;
            color: #1f144a;
            font-size: 12px;
        }
        
        .notes-box {
            background: #f5f5f5;
            border-right: 4px solid #ef4870;
            padding: 10px;
            margin: 25px 0;
            line-height: 1.7;
            page-break-inside: avoid;
        }
        
        .notes-title {
            font-weight: bold;
            color: #ef4870;
            margin-bottom: 8px;
        }
        
        .footer {
            display: none; /* Hidden since letter head provides footer */
        }
        
        .footer-info {
            margin: 5px 0;
        }
        
        .text-center { text-align: center; }
        .text-left { text-align: left; }

        /* Two-column layout for quote info */
        .info-columns { 
            width: 100%; 
            margin-bottom: 15px;
            overflow: hidden;
        }
        .info-col { 
            float: right;
            width: 49%; 
            vertical-align: top;
        }
        .info-col:first-child {
            margin-left: 2%;
        }
        
        /* منع القطع في منتصف الصفوف */
        table.items tr {
            page-break-inside: avoid;
        }
        
        table.items tbody tr {
            page-break-after: auto;
        }
        
        .summary-table {
            page-break-inside: avoid;
            page-break-before: auto;
        }
        
        .notes-box {
            page-break-inside: avoid;
        }
        
        h3 {
            page-break-after: avoid;
        }
        
        .summary-section { margin-top: 40px; }
        .summary-section.new-page { margin-top: 140px; }
        .inline-stamp { text-align: left; margin-top: 12px; }
        .inline-stamp img { width: 66px; height: auto; opacity: 1; }
        
    </style>
</head>
<body>
    <div class="container">
        <!-- Quote Title -->
        <div class="quote-title">
            عرض سعر رقم {{ $quote->quote_number }}
        </div>
        
        <!-- Quote Information -->
        <div class="info-columns">
            <!-- Quote/Customer Info: right side -->
            <div class="info-col">
                <table class="info-table">
                    <tr>
                        <td class="info-label">رقم العرض:</td>
                        <td><strong>{{ $quote->quote_number }}</strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">تاريخ الإصدار:</td>
                        <td>{{ $quote->created_at->format('Y/m/d') }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">اسم العميل:</td>
                        <td><strong>{{ $quote->user->name }}</strong></td>
                    </tr>
                    @if($quote->user->phone)
                    <tr>
                        <td class="info-label">رقم الجوال:</td>
                        <td>{{ $quote->user->phone }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="info-label">البريد الإلكتروني:</td>
                        <td>{{ $quote->user->email }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">الحالة:</td>
                        <td>
                            <span class="status-badge status-{{ $quote->status }}">
                                {{ $quote->status_text }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Company Info: left side -->
            <div class="info-col">
                <table class="info-table">
                    <tr>
                        <td class="info-label">اسم المنصة:</td>
                        <td><strong>Your Events</strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">السجل التجاري:</td>
                        <td><strong>7025605267</strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">الرقم الضريبي:</td>
                        <td><strong>311019444900003</strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">رقم الجوال:</td>
                        <td><strong>+966 50 515 9616</strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">البريد الإلكتروني:</td>
                        <td><strong>hello@yourevents.sa</strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">الموقع الإلكتروني:</td>
                        <td><strong>www.yourevents.sa</strong></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Items Table -->
        <h3>تفاصيل الخدمات</h3>
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 6%;">#</th>
                    <th style="width: 46%;">الخدمة</th>
                    <th style="width: 12%; text-align: center;">الكمية</th>
                    <th style="width: 18%; text-align: right;">السعر ({{ __('common.currency') }})</th>
                    <th style="width: 18%; text-align: right;">المجموع ({{ __('common.currency') }})</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="service-name">{{ $item->service->name ?? $item->service_name }}</div>
                        @php
                            $pdfDesc = $item->service->description ?? $item->service_description;
                        @endphp
                        @if($pdfDesc)
                            <div class="service-description">{{ $pdfDesc }}</div>
                        @endif
                        @if($item->customer_notes)
                            <div class="service-notes">ملاحظات: {{ $item->customer_notes }}</div>
                        @endif

                        {{-- عرض التنويعات المختارة --}}
                        @php
                            $variationId = $item->getSelectedVariationId();
                            $variation = $variationId ? $item->getVariation() : null;
                        @endphp
                        @if(false)
                            <div class="service-options" style="margin-top: 6px; border-top: 1px solid #e0e0e0; padding-top: 4px;">
                                <small style="color: #2563eb; font-weight: bold;">الخيارات المختارة:</small>
                                <ul style="margin: 2px 0 0; padding: 0 18px;"></ul>
                            </div>
                        @endif

                        @php
                            $fieldsBySlug = [];
                            $fieldOptions = [];
                            if ($item->service && is_array($item->service->custom_fields)) {
                                foreach ($item->service->custom_fields as $field) {
                                    $slug = \Illuminate\Support\Str::slug($field['label'] ?? '');
                                    if ($slug) {
                                        $fieldsBySlug[$slug] = $field['label'] ?? $slug;
                                        $opts = $field['options'] ?? [];
                                        if (is_string($opts)) { $opts = array_map('trim', explode(',', $opts)); }
                                        $fieldOptions[$slug] = is_array($opts) ? array_values(array_filter($opts, fn($v) => $v !== null && $v !== '')) : [];
                                    }
                                }
                            }
                            $validSelections = [];
                            if (is_array($item->selections)) {
                                foreach ($item->selections as $key => $val) {
                                    if (str_starts_with($key, '_')) continue;
                                    if (!array_key_exists($key, $fieldsBySlug)) continue;
                                    $allowed = $fieldOptions[$key] ?? [];
                                    if (is_array($val)) {
                                        $vals = array_values(array_filter($val, fn($v) => in_array((string)$v, $allowed, true)));
                                        if (count($vals) > 0) { $validSelections[$key] = $vals; }
                                    } else {
                                        if (in_array((string)$val, $allowed, true)) { $validSelections[$key] = [$val]; }
                                    }
                                }
                            }
                        @endphp
                        @if(count($validSelections) > 0)
                            <div class="service-options">
                                <small>اختياراتك:</small>
                                <ul style="margin: 4px 0 0; padding: 0 18px;">
                                    @foreach($validSelections as $key => $val)
                                        @php 
                                            $label = $fieldsBySlug[$key];
                                        @endphp
                                        <li style="font-size: 11px;">
                                            <strong>{{ $label }}:</strong>
                                            @if(is_array($val))
                                                {{ implode('، ', array_filter($val)) }}
                                            @else
                                                {{ (string)$val }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price, 2) }}</td>
                    <td class="text-right"><strong>{{ number_format($item->subtotal, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="summary-section" style="margin-top: {{ $quote->items->count() > 5 ? 140 : 16 }}px; padding-top: {{ $quote->items->count() > 5 ? 175 : 16 }}px; {{ $quote->items->count() > 5 ? 'page-break-before: always;' : '' }}">
            <table class="summary-table" style="margin-top: 0;">
                <tr>
                    <td class="summary-label">المجموع الفرعي:</td>
                    <td class="summary-value">{{ number_format($quote->subtotal, 2) }} {{ __('common.currency') }}</td>
                </tr>
                <tr>
                    <td class="summary-label">الضريبة (15%):</td>
                    <td class="summary-value">{{ number_format($quote->tax, 2) }} {{ __('common.currency') }}</td>
                </tr>
                @if($quote->discount > 0)
                <tr class="text-success">
                    <td class="summary-label">الخصم:</td>
                    <td class="summary-value">-{{ number_format($quote->discount, 2) }} {{ __('common.currency') }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="summary-label">الإجمالي:</td>
                    <td class="summary-value">{{ number_format($quote->total, 2) }} {{ __('common.currency') }}</td>
                </tr>
            </table>
            
            @if($quote->items->count() <= 5)
            <table style="width: 100%; border-collapse: collapse; margin-top: 8px; table-layout: fixed; line-height: 2;">
                <tr>
                    <td style="width: 74%; vertical-align: top; padding: 0;">
                        <div class="notes-box" style="border-right-color: #f0c71d; margin: 0;">
                            <div class="notes-title" style="color: #f0c71d; margin-bottom: 6px;">معلومات مهمة:</div>
                            <ul style="margin: 0 15px 0 0; padding: 0 18px; font-size: 10px; list-style-position: inside; line-height: 1.3;">
                                <li>عرض السعر صالح لمدة 30 يوم من تاريخ الإصدار</li>
                                <li>الأسعار المذكورة شاملة ضريبة القيمة المضافة (15%)</li>
                                <li>يرجى مراجعة الخدمات والأسعار قبل التأكيد</li>
                                <li>للاستفسارات والتواصل: <span dir="ltr" style="unicode-bidi: bidi-override; direction: ltr;">hello@yourevents.sa</span></li>
                            </ul>
                        </div>
                    </td>
                    <td style="width: 26%; vertical-align: top; padding: 0;">
                        @if(file_exists(public_path('storage/extra/stamp.png')))
                        <div class="inline-stamp">
                            <img src="{{ public_path('storage/extra/stamp.png') }}" alt="ختم">
                        </div>
                        @endif
                    </td>
                </tr>
            </table>
            @else
            <div class="notes-box" style="border-right-color: #f0c71d; margin-top: 80px;">
                <div class="notes-title" style="color: #f0c71d;">معلومات مهمة:</div>
                <ul style="margin: 0 15px 0 0; padding: 0 18px; font-size: 10px; list-style-position: inside;">
                    <li>عرض السعر صالح لمدة 30 يوم من تاريخ الإصدار</li>
                    <li>الأسعار المذكورة شاملة ضريبة القيمة المضافة (15%)</li>
                    <li>يرجى مراجعة الخدمات والأسعار قبل التأكيد</li>
                    <li>للاستفسارات والتواصل: <span dir="ltr" style="unicode-bidi: bidi-override; direction: ltr;">hello@yourevents.sa</span></li>
                </ul>
            </div>
            @if(file_exists(public_path('storage/extra/stamp.png')))
            <div class="inline-stamp">
                <img src="{{ public_path('storage/extra/stamp.png') }}" alt="ختم">
            </div>
            @endif
            @endif
        </div>
        
        @if($quote->customer_notes)
        <div class="notes-box">
            <div class="notes-title">ملاحظات العميل:</div>
            <div>{{ $quote->customer_notes }}</div>
        </div>
        @endif
        
        @if($quote->admin_notes)
        <div class="notes-box" style="border-right-color: #2dbcae;">
            <div class="notes-title" style="color: #2dbcae;">رد الإدارة:</div>
            <div>{{ $quote->admin_notes }}</div>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-info">
                <strong>Your Events</strong> - {{ __('common.site_slogan') }}
            </div>
            <div class="footer-info">
                البريد الإلكتروني: hello@yourevents.sa | الهاتف: +966 50 515 9616
            </div>
            <div class="footer-info">
                تم إنشاء هذا المستند بواسطة نظام Your Events
            </div>
        </div>
    </div>
</body>
</html>
