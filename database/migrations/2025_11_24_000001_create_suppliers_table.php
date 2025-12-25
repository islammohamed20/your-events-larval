<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();

            // معلومات المنشأة / المورد
            $table->enum('supplier_type', ['individual', 'company'])->comment('فرد أو منشأة');
            $table->string('name')->comment('اسم المنشأة / المورد');
            $table->string('commercial_register')->nullable()->comment('السجل التجاري');
            $table->string('tax_number')->nullable()->comment('الرقم الضريبي');
            $table->string('headquarters_city')->comment('مقر المنشأة الرئيسي');
            $table->text('description')->nullable()->comment('نبذة عن الخدمات');

            // الخدمات المقدمة (JSON array)
            $table->json('services_offered')->nullable()->comment('الخدمات التي يقدمها المورد');

            // المرفقات
            $table->string('commercial_register_file')->nullable();
            $table->string('tax_certificate_file')->nullable();
            $table->string('company_profile_file')->nullable();
            $table->json('portfolio_files')->nullable()->comment('أعمال سابقة');

            // معلومات التواصل
            $table->string('primary_phone')->comment('رقم الجوال الأساسي');
            $table->string('secondary_phone')->nullable()->comment('رقم جوال بديل');
            $table->string('email')->unique()->comment('البريد الإلكتروني');
            $table->string('password')->comment('كلمة المرور');
            $table->json('social_media')->nullable()->comment('حسابات التواصل الاجتماعي');
            $table->string('address')->nullable()->comment('العنوان / المدينة');

            // حالة الحساب
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('privacy_accepted')->default(false);

            // OTP
            $table->rememberToken();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('email');
            $table->index('status');
            $table->index('supplier_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
