<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'أفراح وزفاف',
                'name_en' => 'Weddings',
                'description' => 'تنظيم حفلات الزفاف والأفراح بكل تفاصيلها من الديكور والإضاءة إلى التصوير والضيافة',
                'icon' => 'fas fa-ring',
                'color' => '#ef4870',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'مؤتمرات وفعاليات',
                'name_en' => 'Conferences & Events',
                'description' => 'تنظيم المؤتمرات والندوات والفعاليات المهنية والتجارية',
                'icon' => 'fas fa-briefcase',
                'color' => '#1f144a',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'حفلات الأطفال',
                'name_en' => 'Kids Parties',
                'description' => 'تنظيم حفلات أعياد الميلاد والمناسبات الخاصة بالأطفال مع الألعاب والترفيه',
                'icon' => 'fas fa-birthday-cake',
                'color' => '#f0c71d',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'مناسبات اجتماعية',
                'name_en' => 'Social Events',
                'description' => 'تنظيم المناسبات الاجتماعية والعائلية والتجمعات',
                'icon' => 'fas fa-users',
                'color' => '#2dbcae',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'معارض تجارية',
                'name_en' => 'Trade Shows',
                'description' => 'تنظيم المعارض التجارية وعرض المنتجات والخدمات',
                'icon' => 'fas fa-store',
                'color' => '#7269b0',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'حفلات تخرج',
                'name_en' => 'Graduation',
                'description' => 'تنظيم حفلات التخرج الجامعية والمدرسية',
                'icon' => 'fas fa-graduation-cap',
                'color' => '#28a745',
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
