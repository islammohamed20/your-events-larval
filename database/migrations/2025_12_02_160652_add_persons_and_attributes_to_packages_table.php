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
        Schema::table('packages', function (Blueprint $table) {
            $table->integer('persons_count')->nullable()->after('price')->comment('عدد الأشخاص');
            $table->json('attributes')->nullable()->after('features')->comment('خواص الباقة مع الوصف والتفاصيل');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['persons_count', 'attributes']);
        });
    }
};
