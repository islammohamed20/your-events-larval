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
            $table->string('company_name')->after('name'); // اسم الجهة (إجباري)
            $table->string('tax_number')->nullable()->after('company_name'); // الرقم الضريبي (اختياري)
            
            $table->index('company_name');
            $table->index('tax_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['company_name']);
            $table->dropIndex(['tax_number']);
            $table->dropColumn(['company_name', 'tax_number']);
        });
    }
};
