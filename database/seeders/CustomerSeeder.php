<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الحصول على أول 5 مستخدمين
        $users = User::where('is_admin', false)
            ->limit(5)
            ->get();

        // البيانات الوهمية للعملاء
        $citiesData = [
            'الرياض',
            'جدة',
            'الدمام',
            'الكويت',
            'الشرقية',
        ];

        foreach ($users as $index => $user) {
            Customer::create([
                'user_id' => $user->id,
                'company_name' => 'شركة '.$user->name,
                'phone' => '050'.rand(10000000, 99999999),
                'city' => $citiesData[$index] ?? 'الرياض',
                'status' => 'active',
                'is_verified' => true,
                'registered_at' => now(),
            ]);
        }

        echo '✅ تم إضافة '.count($users)." عميل بنجاح\n";
    }
}
