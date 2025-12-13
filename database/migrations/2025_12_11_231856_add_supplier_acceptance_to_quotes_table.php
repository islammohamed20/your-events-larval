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
            $table->foreignId('accepted_by_supplier_id')->nullable()->after('rejected_at')->constrained('suppliers')->onDelete('set null');
            $table->timestamp('supplier_accepted_at')->nullable()->after('accepted_by_supplier_id');
            $table->text('supplier_notes')->nullable()->after('supplier_accepted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropForeign(['accepted_by_supplier_id']);
            $table->dropColumn(['accepted_by_supplier_id', 'supplier_accepted_at', 'supplier_notes']);
        });
    }
};
