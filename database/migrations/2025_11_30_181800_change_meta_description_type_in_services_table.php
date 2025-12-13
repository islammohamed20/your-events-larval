<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change meta_description to TEXT to allow long SEO descriptions
        Schema::table('services', function (Blueprint $table) {
            // Use raw SQL to avoid doctrine/dbal requirement for change()
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE services MODIFY meta_description TEXT NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to VARCHAR(160) if needed
        Schema::table('services', function (Blueprint $table) {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE services MODIFY meta_description VARCHAR(160) NULL');
        });
    }
};

