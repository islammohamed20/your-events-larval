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
        Schema::table('quotes', function (Blueprint $table) {
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid')->after('status');
            $table->timestamp('payment_date')->nullable()->after('payment_status');
            $table->string('payment_method')->nullable()->after('payment_date');
            $table->string('payment_reference')->nullable()->after('payment_method');
            $table->text('payment_notes')->nullable()->after('payment_reference');
        });
        
        // Update status enum to remove 'booked' and add 'paid'
        DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('pending', 'under_review', 'approved', 'rejected', 'completed', 'paid') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_date',
                'payment_method',
                'payment_reference',
                'payment_notes'
            ]);
        });
        
        DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'booked') DEFAULT 'pending'");
    }
};
