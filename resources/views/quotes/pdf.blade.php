<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="utf-8">
    <title>عرض السعر #{{ $quote->quote_number }}</title>
    <style>
        body {
            font-family: 'dejavusans', sans-serif;
            direction: rtl;
            text-align: right;
            font-size: 11px;
            line-height: 1.5;
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
            padding: 8px;
            border: 1px solid #ddd;
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
        
        h3 {
            color: #1f144a;
            margin: 20px 0 12px 0;
            font-size: 14px;
        }
        
        table.items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
            background: #fff; /* خلفية صلبة */
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
            padding: 8px 10px;
            border: 1px solid #ddd;
            font-size: 10px;
            background: #fff;
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
            font-size: 9px;
            color: #666;
            margin-top: 3px;
        }
        
        .service-notes {
            font-size: 9px;
            color: #0066cc;
            margin-top: 3px;
            font-style: italic;
        }
        
        .summary-table {
            width: 60%;
            margin-right: auto;
            border-collapse: collapse;
            margin-top: 10px;
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
            background: #f2f2f7; /* لون فاتح لضمان وضوح النص */
            color: #1f144a;
            font-size: 12px;
        }
        
        .notes-box {
            background: #f5f5f5;
            border-right: 4px solid #ef4870;
            padding: 10px;
            margin: 15px 0;
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

        /* Two-column layout for quote info (robust for mPDF) */
        .info-columns { width: 100%; margin-bottom: 20px; overflow: hidden; }
        .info-col { float: left; width: 48%; vertical-align: top; }
        .info-col.right { float: right; }
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
            <!-- Company Info: left side -->
            <div class="info-col left">
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
                        <td><strong>sales@yourevents.sa</strong></td>
                    </tr>
                    <tr>
                        <td class="info-label">الموقع الإلكتروني:</td>
                        <td><strong>www.yourevents.sa</strong></td>
                    </tr>
                </table>
            </div>

            <!-- Quote/Customer Info: right side -->
            <div class="info-col right">
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
                        <td>{{ $quote->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="info-label">اسم الجهة:</td>
                        <td><strong>{{ $quote->user->company_name }}</strong></td>
                    </tr>
                    @if($quote->user->tax_number)
                    <tr>
                        <td class="info-label">الرقم الضريبي:</td>
                        <td>{{ $quote->user->tax_number }}</td>
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
        </div>
        
        <!-- Items Table -->
        <h3>تفاصيل الخدمات</h3>
        <table class="items">
            <thead>
                <tr>
                    <th style="width: 6%;">#</th>
                    <th style="width: 46%;">الخدمة</th>
                    <th style="width: 12%; text-align: center;">الكمية</th>
                    <th style="width: 18%; text-align: right;">السعر (ريال)</th>
                    <th style="width: 18%; text-align: right;">المجموع (ريال)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <div class="service-name">{{ $item->service_name }}</div>
                        @if($item->service_description)
                            <div class="service-description">{{ $item->service_description }}</div>
                        @endif
                        @if($item->customer_notes)
                            <div class="service-notes">ملاحظات: {{ $item->customer_notes }}</div>
                        @endif

                        {{-- عرض التنويعات المختارة --}}
                        @php
                            $variationId = $item->getSelectedVariationId();
                            $variation = $variationId ? $item->getVariation() : null;
                        @endphp
                        @if($variation && $variation->attributeValuesList && $variation->attributeValuesList->count() > 0)
                            <div class="service-options" style="margin-top: 6px; border-top: 1px solid #e0e0e0; padding-top: 4px;">
                                <small style="color: #2563eb; font-weight: bold;">الخيارات المختارة:</small>
                                <ul style="margin: 2px 0 0; padding: 0 18px;">
                                    @foreach($variation->attributeValuesList as $value)
                                        <li style="font-size: 11px; color: #1e40af;">
                                            <strong>{{ $value->attribute->name }}:</strong> {{ $value->value }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @php
                            $fieldsBySlug = [];
                            if ($item->service && is_array($item->service->custom_fields)) {
                                foreach ($item->service->custom_fields as $field) {
                                    $slug = \Illuminate\Support\Str::slug($field['label'] ?? '');
                                    if ($slug) {
                                        $fieldsBySlug[$slug] = $field['label'] ?? $slug;
                                    }
                                }
                            }
                        @endphp
                        @if(is_array($item->selections) && count($item->selections) > 0)
                            <div class="service-options">
                                <small>اختياراتك:</small>
                                <ul style="margin: 4px 0 0; padding: 0 18px;">
                                    @foreach($item->selections as $key => $val)
                                        @php 
                                            // تجاهل المفاتيح الداخلية
                                            if (str_starts_with($key, '_')) continue;
                                            
                                            $label = $fieldsBySlug[$key] ?? \Illuminate\Support\Str::title(str_replace('-', ' ', $key)); 
                                        @endphp
                                        <li style="font-size: 11px;">
                                            <strong>{{ $label }}:</strong>
                                            @if(is_array($val))
                                                {{ implode('، ', array_filter($val)) }}
                                            @else
                                                {{ $val }}
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
        
        <!-- Summary -->
        <table class="summary-table">
            <tr>
                <td class="summary-label">المجموع الفرعي:</td>
                <td class="summary-value">{{ number_format($quote->subtotal, 2) }} ريال</td>
            </tr>
            <tr>
                <td class="summary-label">الضريبة (15%):</td>
                <td class="summary-value">{{ number_format($quote->tax, 2) }} ريال</td>
            </tr>
            @if($quote->discount > 0)
            <tr class="text-success">
                <td class="summary-label">الخصم:</td>
                <td class="summary-value">-{{ number_format($quote->discount, 2) }} ريال</td>
            </tr>
            @endif
            <tr class="total-row">
                <td class="summary-label">الإجمالي:</td>
                <td class="summary-value">{{ number_format($quote->total, 2) }} ريال</td>
            </tr>
        </table>
        
        <!-- Customer Notes -->
        @if($quote->customer_notes)
        <div class="notes-box">
            <div class="notes-title">ملاحظات العميل:</div>
            <div>{{ $quote->customer_notes }}</div>
        </div>
        @endif
        
        <!-- Admin Notes -->
        @if($quote->admin_notes)
        <div class="notes-box" style="border-right-color: #2dbcae;">
            <div class="notes-title" style="color: #2dbcae;">رد الإدارة:</div>
            <div>{{ $quote->admin_notes }}</div>
        </div>
        @endif
        
        <!-- Important Information -->
        <div class="notes-box" style="border-right-color: #f0c71d;">
            <div class="notes-title" style="color: #f0c71d;">معلومات مهمة:</div>
            <div style="margin-right: 15px; font-size: 10px;">
                • عرض السعر صالح لمدة 30 يوم من تاريخ الإصدار<br>
                • الأسعار المذكورة شاملة ضريبة القيمة المضافة (15%)<br>
                • يرجى مراجعة الخدمات والأسعار قبل التأكيد<br>
                • للاستفسارات والتواصل: hello@yourevents.sa
            </div>
        </div>
        
        <!-- Stamp on the left -->
        <div style="text-align: left; margin-top: 15px; margin-left: 70px;">
            <img src="{{ public_path('storage/extra/stamp.png') }}" alt="ختم الشركة" style="width: 120px; height: auto;">
        </div>
        
        <!-- Footer -->
        <div class="footer">
            <div class="footer-info">
                <strong>Your Events</strong> - حوّل مناسبتك العادية إلى لحظة استثنائية
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
