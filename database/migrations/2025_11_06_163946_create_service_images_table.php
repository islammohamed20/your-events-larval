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
        Schema::create('service_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('image_path'); // مسار الصورة
            $table->string('alt_text')->nullable(); // نص بديل للصورة
            $table->boolean('is_thumbnail')->default(false); // هل هي صورة مصغرة؟
            $table->integer('sort_order')->default(0); // ترتيب العرض
            $table->timestamps();

            // فهرس للبحث السريع عن الصورة المصغرة
            $table->index(['service_id', 'is_thumbnail']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_images');
    }
};
