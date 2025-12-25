<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return User::where('is_admin', false)
            ->where('role', 'user')
            ->withCount(['quotes', 'bookings'])
            ->withSum(['bookings' => function ($query) {
                $query->where('status', 'confirmed');
            }], 'total_amount')
            ->get();
    }

    /**
     * @param  mixed  $customer
     */
    public function map($customer): array
    {
        return [
            $customer->id,
            $customer->name,
            $customer->email,
            $customer->phone,
            $customer->company_name,
            $customer->tax_number,
            $customer->quotes_count,
            $customer->bookings_count,
            number_format($customer->bookings_sum_total_amount ?? 0, 2),
            $customer->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return [
            'رقم العميل',
            'الاسم',
            'البريد الإلكتروني',
            'رقم الهاتف',
            'اسم الشركة',
            'الرقم الضريبي',
            'عدد عروض الأسعار',
            'عدد الحجوزات',
            'إجمالي المدفوعات',
            'تاريخ التسجيل',
        ];
    }

    /**
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
