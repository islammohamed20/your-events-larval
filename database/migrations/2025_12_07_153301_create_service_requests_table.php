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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade'); // الحجز
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // الفئة
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade'); // الخدمة
            $table->integer('quantity')->default(1); // الكمية
            $table->decimal('price', 10, 2)->nullable(); // السعر
            $table->text('customer_notes')->nullable(); // ملاحظات العميل
            $table->text('admin_notes')->nullable(); // ملاحظات عامة (الإدارة)
            $table->enum('status', ['pending', 'accepted', 'rejected', 'completed'])->default('pending'); // حالة الطلب
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
