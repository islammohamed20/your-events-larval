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
        // Modify the status column to include 'under_review'
        DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('pending', 'under_review', 'approved', 'rejected', 'completed', 'booked') DEFAULT 'under_review'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to old enum (remove 'under_review')
        DB::statement("ALTER TABLE quotes MODIFY COLUMN status ENUM('pending', 'approved', 'rejected', 'completed', 'booked') DEFAULT 'pending'");
    }
};
