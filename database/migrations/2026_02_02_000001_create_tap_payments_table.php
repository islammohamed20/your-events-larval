<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tap_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('quote_id')->nullable()->constrained()->onDelete('set null');
            $table->string('tap_charge_id');
            $table->string('tap_transaction_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('currency', 3)->nullable();
            $table->string('status')->nullable();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->json('charge_data')->nullable();
            $table->timestamps();
            $table->index('tap_charge_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tap_payments');
    }
};
