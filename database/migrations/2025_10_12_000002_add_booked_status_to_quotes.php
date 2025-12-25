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
        // Add 'booked' status to quotes table
        DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'booked') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove 'booked' status from quotes table
        DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed') DEFAULT 'pending'");
    }
};
