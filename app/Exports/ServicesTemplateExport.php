<?php

namespace App\Exports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServicesTemplateExport implements FromArray, WithHeadings, WithStyles
{
    /**
     * @return array
     */
    public function array(): array
    {
        // Return empty rows as template with examples
        return [
            [
                '', // ID (فارغ للخدمات الجديدة، أو رقم للتحديث)
                '', // name
                '', // subtitle
                '', // category_id
                '', // description
                '', // marketing_description
                '', // what_we_offer
                '', // why_choose_us
                '', // meta_description
                '', // price
                '', // duration
                '', // type
                '', // is_active
            ],
            [
                '', // اتركه فارغ لإنشاء خدمة جديدة
                'مثال: تأجير بلايستيشن 5',
                'PlayStation 5, PS5',
                '2',
                'تأجير جهاز بلايستيشن 5 أحدث الإصدارات',
                'استمتع بتجربة ألعاب فريدة مع أحدث أجهزة بلايستيشن 5',
                '- جهاز بلايستيشن 5\n- يدين تحكم\n- 10 ألعاب',
                'أحدث الأجهزة - أسعار تنافسية - توصيل مجاني',
                'تأجير بلايستيشن 5 في الرياض - أفضل الأسعار',
                '500',
                '3 أيام',
                'ألعاب',
                'نعم',
            ],
            [
                '5', // مثال: ضع ID=5 لتحديث خدمة موجودة برقم 5
                'مثال: تحديث خدمة موجودة',
                '',
                '3',
                'وصف محدث للخدمة',
                'وصف تسويقي محدث',
                '',
                '',
                '',
                '750',
                '5 أيام',
                'تصوير',
                'نعم',
            ],
        ];
    }

    /**
     * Define column headings
     */
    public function headings(): array
    {
        return [
            'id',
            'name',
            'subtitle',
            'category_id',
            'description',
            'marketing_description',
            'what_we_offer',
            'why_choose_us',
            'meta_description',
            'price',
            'duration',
            'type',
            'is_active',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Get categories for the note
        $categories = Category::all()->pluck('name', 'id')->toArray();
        $categoriesText = '';
        foreach ($categories as $id => $name) {
            $categoriesText .= "$id=$name, ";
        }
        
        // Add notes to headers
        $sheet->getComment('A1')->getText()->createTextRun('رقم التعريف (فارغ=إنشاء، رقم=تحديث)');
        $sheet->getComment('B1')->getText()->createTextRun('اسم الخدمة (مطلوب)');
        $sheet->getComment('C1')->getText()->createTextRun('العنوان الفرعي (للألعاب فقط)');
        $sheet->getComment('D1')->getText()->createTextRun('رقم الفئة: ' . rtrim($categoriesText, ', '));
        $sheet->getComment('E1')->getText()->createTextRun('الوصف');
        $sheet->getComment('F1')->getText()->createTextRun('الوصف التسويقي');
        $sheet->getComment('G1')->getText()->createTextRun('وش نوفر؟');
        $sheet->getComment('H1')->getText()->createTextRun('ليش تختار Your Events؟');
        $sheet->getComment('I1')->getText()->createTextRun('وصف SEO (160 حرف)');
        $sheet->getComment('J1')->getText()->createTextRun('السعر');
        $sheet->getComment('K1')->getText()->createTextRun('المدة');
        $sheet->getComment('L1')->getText()->createTextRun('النوع');
        $sheet->getComment('M1')->getText()->createTextRun('نشطة (نعم/لا)');
        
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            ],
            2 => [
                'font' => ['italic' => true, 'color' => ['rgb' => '808080']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'F2F2F2']],
            ],
        ];
    }
}

