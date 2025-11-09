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
        Schema::table('services', function (Blueprint $table) {
            $table->text('marketing_description')->nullable()->after('description');
            $table->text('what_we_offer')->nullable()->after('marketing_description');
            $table->text('why_choose_us')->nullable()->after('what_we_offer');
            $table->string('meta_description', 160)->nullable()->after('why_choose_us');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['marketing_description', 'what_we_offer', 'why_choose_us', 'meta_description']);
        });
    }
};
