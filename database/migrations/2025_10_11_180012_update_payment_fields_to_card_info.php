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
        Schema::table('users', function (Blueprint $table) {
            // Drop old payment fields
            $table->dropIndex(['bank_account_number']);
            $table->dropIndex(['iban']);
            $table->dropColumn(['bank_name', 'bank_account_number', 'iban']);
            
            // Add new card payment fields
            $table->enum('card_type', ['visa', 'mastercard', 'mada'])->nullable()->after('tax_number');
            $table->string('card_holder_name')->nullable()->after('card_type');
            $table->string('card_last_four', 4)->nullable()->after('card_holder_name'); // آخر 4 أرقام فقط للأمان
            $table->string('card_expiry_month', 2)->nullable()->after('card_last_four'); // MM
            $table->string('card_expiry_year', 4)->nullable()->after('card_expiry_month'); // YYYY
            
            // Add indexes
            $table->index('card_type');
            $table->index('card_last_four');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop new fields
            $table->dropIndex(['card_type']);
            $table->dropIndex(['card_last_four']);
            $table->dropColumn(['card_type', 'card_holder_name', 'card_last_four', 'card_expiry_month', 'card_expiry_year']);
            
            // Restore old fields
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('iban')->nullable();
            $table->index('bank_account_number');
            $table->index('iban');
        });
    }
};
