<?php

namespace Database\Seeders;

use App\Models\Gallery;
use App\Models\Package;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Note: No default admin user created for security reasons
        // Create admin user manually using: php artisan tinker

        // Create sample user (optional - remove in production)
        // User::create([
        //     'name' => 'أحمد محمد',
        //     'email' => 'user@example.com',
        //     'password' => Hash::make('password123'),
        //     'phone' => '+966509876543',
        //     'role' => 'user',
        //     'email_verified_at' => now(),
        // ]);

        // Create Services
        $services = [
            [
                'name' => 'تنظيم الأفراح',
                'description' => 'نقدم خدمات تنظيم الأفراح والزفاف بأعلى مستويات الجودة والإتقان. نهتم بكل التفاصيل من التزيين والإضاءة إلى تنسيق الطعام والترفيه لجعل يومكم الخاص لا يُنسى.',
                'is_active' => true,
            ],
            [
                'name' => 'المناسبات الاجتماعية',
                'description' => 'تنظيم جميع أنواع المناسبات الاجتماعية مثل حفلات التخرج، أعياد الميلاد، الذكريات السنوية، والتجمعات العائلية. نضمن لكم تجربة ممتعة ومميزة.',
                'is_active' => true,
            ],
            [
                'name' => 'المؤتمرات والفعاليات',
                'description' => 'خدمات تنظيم المؤتمرات والفعاليات المهنية بمعايير عالمية. نوفر كافة التجهيزات التقنية والإعلامية المطلوبة لضمان نجاح فعاليتكم.',
                'is_active' => true,
            ],
            [
                'name' => 'حفلات الأطفال',
                'description' => 'تنظيم حفلات أطفال مبهجة وآمنة مع ألعاب وأنشطة ترفيهية متنوعة. نهتم بتوفير بيئة ممتعة وآمنة للأطفال مع برامج ترفيهية مناسبة لأعمارهم.',
                'is_active' => true,
            ],
            [
                'name' => 'المعارض التجارية',
                'description' => 'تصميم وتنظيم المعارض التجارية والترويجية. نقدم حلول شاملة تشمل التصميم، البناء، والإدارة لضمان نجاح معرضكم التجاري.',
                'is_active' => true,
            ],
            [
                'name' => 'الحفلات الخيرية',
                'description' => 'تنظيم الحفلات والفعاليات الخيرية بروح إنسانية عالية. نساعدكم في تحقيق أهدافكم الخيرية من خلال فعاليات منظمة ومؤثرة.',
                'is_active' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Create Packages
        $packages = [
            [
                'name' => 'الباقة الذهبية',
                'price' => 15000.00,
                'description' => 'الباقة الأكثر شمولية وفخامة لمناسباتكم الخاصة. تشمل جميع الخدمات المتميزة مع أعلى مستويات الجودة.',
                'features' => [
                    'تنسيق وتزيين المكان بالكامل',
                    'خدمة ضيافة VIP مع أفضل أنواع الطعام',
                    'نظام صوتي ومرئي متطور',
                    'تصوير فوتوغرافي وفيديوجرافي احترافي',
                    'منسق شخصي مخصص',
                    'خدمة استقبال وتوديع الضيوف',
                    'تنظيف المكان بعد الحفل',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'الباقة الفضية',
                'price' => 10000.00,
                'description' => 'باقة متوسطة تجمع بين الجودة والسعر المناسب. مثالية للمناسبات المتوسطة التي تتطلب خدمات عالية الجودة.',
                'features' => [
                    'تزيين أساسي للمكان',
                    'خدمة طعام وشراب متنوعة',
                    'نظام صوتي أساسي',
                    'تصوير فوتوغرافي',
                    'منسق للفعالية',
                    'خدمة تنظيف أساسية',
                ],
                'is_active' => true,
            ],
            [
                'name' => 'الباقة البرونزية',
                'price' => 6000.00,
                'description' => 'باقة اقتصادية مناسبة للمناسبات الصغيرة والمتوسطة. تقدم الخدمات الأساسية بجودة ممتازة.',
                'features' => [
                    'تزيين بسيط ومناسب',
                    'خدمة طعام أساسية',
                    'نظام صوتي بسيط',
                    'منسق للإشراف',
                    'خدمة تنظيف',
                ],
                'is_active' => true,
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }

        // Create Gallery Items
        $galleryItems = [
            [
                'title' => 'حفل زفاف أسطوري',
                'type' => 'image',
                'path' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?ixlib=rb-4.0.3',
                'description' => 'حفل زفاف منظم بأعلى مستويات الأناقة والفخامة',
                'is_featured' => true,
            ],
            [
                'title' => 'مؤتمر تقني متميز',
                'type' => 'image',
                'path' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3',
                'description' => 'تنظيم مؤتمر تقني بمعايير عالمية',
                'is_featured' => true,
            ],
            [
                'title' => 'حفل عيد ميلاد مميز',
                'type' => 'image',
                'path' => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?ixlib=rb-4.0.3',
                'description' => 'احتفال بعيد ميلاد بتنسيق رائع',
                'is_featured' => true,
            ],
            [
                'title' => 'حفل تخرج جامعي',
                'type' => 'image',
                'path' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3',
                'description' => 'تنظيم احتفالية التخرج بأجواء مميزة',
                'is_featured' => false,
            ],
        ];

        foreach ($galleryItems as $item) {
            Gallery::create($item);
        }

        // Create Reviews
        $reviews = [
            [
                'client_name' => 'سارة العتيبي',
                'rating' => 5,
                'comment' => 'خدمة ممتازة وتنظيم رائع! فريق Your Events جعل حفل زفافي حلماً يتحقق. كل التفاصيل كانت مثالية والتنسيق كان في غاية الروعة.',
                'is_approved' => true,
            ],
            [
                'client_name' => 'محمد الأحمد',
                'rating' => 5,
                'comment' => 'تجربة مميزة جداً في تنظيم مؤتمر شركتنا السنوي. الفريق محترف ومتفهم لاحتياجاتنا. أنصح بهم بشدة.',
                'is_approved' => true,
            ],
            [
                'client_name' => 'نورا القحطاني',
                'rating' => 4,
                'comment' => 'نظموا حفل تخرج ابنتي بشكل رائع. الديكور كان جميل والتنظيم ممتاز. شكراً لكم على هذه التجربة الرائعة.',
                'is_approved' => true,
            ],
        ];

        foreach ($reviews as $review) {
            Review::create($review);
        }

        // Seed Email Templates (booking, welcome, supplier approval)
        $this->call(EmailTemplatesSeeder::class);

        // Seed WhatsApp message templates
        $this->call(WhatsAppMessageTemplatesSeeder::class);
    }
}
