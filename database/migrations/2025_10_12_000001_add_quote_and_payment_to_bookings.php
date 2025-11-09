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
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('quote_id')->nullable()->after('user_id')->constrained()->onDelete('set null');
            $table->enum('payment_method', ['card', 'bank_transfer', 'cash'])->nullable()->after('total_amount');
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending')->after('payment_method');
            $table->text('payment_notes')->nullable()->after('payment_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['quote_id']);
            $table->dropColumn(['quote_id', 'payment_method', 'payment_status', 'payment_notes']);
        });
    }
};
