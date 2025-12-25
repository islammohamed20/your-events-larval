<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomerDetailExport implements WithMultipleSheets
{
    protected $customer;

    public function __construct(User $customer)
    {
        $this->customer = $customer;
    }

    public function sheets(): array
    {
        $sheets = [];

        // Customer Info Sheet
        $sheets[] = new CustomerInfoSheet($this->customer);

        // Quotes Sheet
        if ($this->customer->quotes->count() > 0) {
            $sheets[] = new CustomerQuotesSheet($this->customer);
        }

        // Payments Sheet
        if ($this->customer->bookings->count() > 0) {
            $sheets[] = new CustomerPaymentsSheet($this->customer);
        }

        return $sheets;
    }
}

class CustomerInfoSheet implements FromArray, WithTitle
{
    protected $customer;

    public function __construct(User $customer)
    {
        $this->customer = $customer;
    }

    public function array(): array
    {
        return [
            ['معلومات العميل'],
            [''],
            ['رقم العميل', $this->customer->id],
            ['الاسم', $this->customer->name],
            ['البريد الإلكتروني', $this->customer->email],
            ['رقم الهاتف', $this->customer->phone],
            ['اسم الشركة', $this->customer->company_name],
            ['الرقم الضريبي', $this->customer->tax_number],
            ['تاريخ التسجيل', $this->customer->created_at->format('Y-m-d H:i:s')],
            [''],
            ['الإحصائيات'],
            ['عدد عروض الأسعار', $this->customer->quotes->count()],
            ['عدد الحجوزات', $this->customer->bookings->count()],
            ['إجمالي المدفوعات', number_format($this->customer->bookings->where('status', 'confirmed')->sum('total_amount'), 2)],
        ];
    }

    public function title(): string
    {
        return 'معلومات العميل';
    }
}

class CustomerQuotesSheet implements FromArray, WithTitle
{
    protected $customer;

    public function __construct(User $customer)
    {
        $this->customer = $customer;
    }

    public function array(): array
    {
        $data = [
            ['عروض الأسعار'],
            [''],
            ['رقم العرض', 'الحالة', 'المجموع الفرعي', 'الضريبة', 'الخصم', 'الإجمالي', 'تاريخ الإنشاء'],
        ];

        foreach ($this->customer->quotes as $quote) {
            $data[] = [
                $quote->quote_number,
                $quote->status,
                number_format($quote->subtotal, 2),
                number_format($quote->tax, 2),
                number_format($quote->discount, 2),
                number_format($quote->total, 2),
                $quote->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $data;
    }

    public function title(): string
    {
        return 'عروض الأسعار';
    }
}

class CustomerPaymentsSheet implements FromArray, WithTitle
{
    protected $customer;

    public function __construct(User $customer)
    {
        $this->customer = $customer;
    }

    public function array(): array
    {
        $data = [
            ['المدفوعات المكتملة'],
            [''],
            ['رقم المرجع', 'اسم العميل', 'تاريخ الحدث', 'المبلغ', 'الحالة', 'تاريخ الحجز'],
        ];

        foreach ($this->customer->bookings->where('status', 'confirmed') as $booking) {
            $data[] = [
                $booking->booking_reference,
                $booking->client_name,
                $booking->event_date ? $booking->event_date->format('Y-m-d') : '',
                number_format($booking->total_amount, 2),
                $booking->status,
                $booking->created_at->format('Y-m-d H:i:s'),
            ];
        }

        return $data;
    }

    public function title(): string
    {
        return 'المدفوعات';
    }
}
