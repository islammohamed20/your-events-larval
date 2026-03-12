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
        Schema::table('supplier_services', function (Blueprint $table) {
            $table->decimal('supplier_price', 10, 2)->nullable()->after('is_available');
            $table->decimal('public_price', 10, 2)->nullable()->after('supplier_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_services', function (Blueprint $table) {
            $table->dropColumn(['supplier_price', 'public_price']);
        });
    }
};
