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
        Schema::create('booking_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('supplier_id')->constrained()->onDelete('cascade');
            $table->timestamp('notified_at')->useCurrent();
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->enum('response', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // Unique constraint: مورد واحد لا يمكن أن يحصل على نفس الحجز مرتين
            $table->unique(['booking_id', 'supplier_id']);

            // Indexes للأداء
            $table->index('booking_id');
            $table->index('supplier_id');
            $table->index(['response', 'notified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_notifications');
    }
};
