<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $db = config('database.connections.mysql.database');

        if (!empty($db)) {
            // Ensure database default charset/collation supports Arabic
            DB::statement("ALTER DATABASE `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        }

        // Convert services table to utf8mb4 to avoid Incorrect string value errors
        DB::statement("ALTER TABLE `services` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op: keeping utf8mb4 as the safe default for multilingual content
    }
};