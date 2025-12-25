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
        Schema::create('competitive_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // معلومات العميل
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();

            // تفاصيل الحدث
            $table->date('event_date');
            $table->time('event_time')->nullable();
            $table->string('event_location')->nullable();
            $table->integer('guests_count')->nullable();
            $table->text('notes')->nullable();

            // الحالة والتوقيت
            $table->enum('status', ['pending', 'accepted', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('expires_at')->nullable(); // متى ينتهي وقت قبول الطلب

            // معلومات المورد الفائز
            $table->foreignId('accepted_by_supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->timestamp('accepted_at')->nullable();
            $table->text('supplier_notes')->nullable();

            // الإحصائيات
            $table->integer('notified_suppliers_count')->default(0);
            $table->integer('views_count')->default(0);

            $table->timestamps();
            $table->softDeletes();

            // فهارس للأداء
            $table->index('status');
            $table->index('expires_at');
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competitive_orders');
    }
};
