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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Booking reference
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Payment gateway
            $table->enum('gateway', ['moyasar', 'hyperpay', 'paytabs', 'tap', 'manual'])->default('moyasar');
            $table->string('gateway_payment_id')->nullable(); // Payment ID from gateway
            $table->string('gateway_transaction_id')->nullable(); // Transaction ID from gateway

            // Payment details
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('SAR');
            $table->enum('payment_method', ['mada', 'visa', 'mastercard', 'applepay', 'stcpay', 'cash', 'bank_transfer'])->nullable();

            // Status
            $table->enum('status', ['pending', 'processing', 'paid', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            // Additional info
            $table->text('description')->nullable();
            $table->text('failure_reason')->nullable();
            $table->json('metadata')->nullable(); // Gateway response data

            // Invoice
            $table->string('invoice_number')->unique()->nullable();
            $table->string('invoice_url')->nullable();

            // Refund
            $table->decimal('refund_amount', 10, 2)->default(0);
            $table->text('refund_reason')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('booking_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('gateway');
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
