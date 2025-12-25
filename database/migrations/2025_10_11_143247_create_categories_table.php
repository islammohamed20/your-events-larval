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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الفئة
            $table->string('name_en')->nullable(); // الاسم بالإنجليزية (اختياري)
            $table->text('description')->nullable(); // وصف الفئة
            $table->string('icon')->nullable(); // أيقونة الفئة (FontAwesome class)
            $table->string('color')->default('#1f144a'); // لون الفئة
            $table->string('image')->nullable(); // صورة الفئة
            $table->integer('order')->default(0); // ترتيب العرض
            $table->boolean('is_active')->default(true); // نشطة أم لا
            $table->timestamps();

            $table->index('is_active');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
