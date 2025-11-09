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
        // جدول الخصائص (مثل: عدد الأشخاص، المدينة، نوع الحفلة)
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الخاصية (عدد الأشخاص)
            $table->string('slug')->unique(); // اسم نظيف للاستخدام في الكود
            $table->string('type')->default('select'); // نوع الحقل: select, radio, checkbox
            $table->integer('order')->default(0); // ترتيب الظهور
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول قيم الخصائص (مثل: 50-100، 100-200، الرياض، جدة)
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->string('value'); // القيمة (50-100 شخص)
            $table->string('slug'); // اسم نظيف
            $table->integer('order')->default(0); // ترتيب الظهور
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // جدول ربط الخدمات بالخصائص (خدمة معينة تستخدم خاصية معينة)
        Schema::create('attribute_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(0); // ترتيب ظهور الخاصية للخدمة
            $table->timestamps();

            $table->unique(['service_id', 'attribute_id']);
        });

        // جدول التنويعات (كل تركيبة من الخصائص لها سعر)
        Schema::create('service_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('sku')->nullable()->unique(); // كود التعريف
            $table->json('attributes'); // تركيبة الخصائص: {"guests": "50-100", "city": "riyadh"}
            $table->decimal('price', 10, 2); // السعر لهذه التركيبة
            $table->decimal('sale_price', 10, 2)->nullable(); // سعر الخصم
            $table->integer('stock')->nullable(); // الكمية المتاحة (اختياري)
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_variations');
        Schema::dropIfExists('attribute_service');
        Schema::dropIfExists('attribute_values');
        Schema::dropIfExists('attributes');
    }
};
