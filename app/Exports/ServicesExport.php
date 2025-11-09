<?php

namespace App\Exports;

use App\Models\Service;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ServicesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Service::with('category')->get();
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
     * Map data to columns
     */
    public function map($service): array
    {
        return [
            $service->id,
            $service->name,
            $service->subtitle ?? '',
            $service->category_id ?? '',
            $service->description ?? '',
            $service->marketing_description ?? '',
            $service->what_we_offer ?? '',
            $service->why_choose_us ?? '',
            $service->meta_description ?? '',
            $service->price ?? '',
            $service->duration ?? '',
            $service->type ?? '',
            $service->is_active ? 'نعم' : 'لا',
        ];
    }

    /**
     * Style the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        // Get categories for the note
        $categories = \App\Models\Category::all()->pluck('name', 'id')->toArray();
        $categoriesText = '';
        foreach ($categories as $id => $name) {
            $categoriesText .= "$id=$name, ";
        }
        
        // Add notes to important headers
        $sheet->getComment('A1')->getText()->createTextRun('رقم التعريف');
        $sheet->getComment('B1')->getText()->createTextRun('اسم الخدمة (مطلوب)');
        $sheet->getComment('C1')->getText()->createTextRun('العنوان الفرعي');
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
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']],
            ],
        ];
    }
}
