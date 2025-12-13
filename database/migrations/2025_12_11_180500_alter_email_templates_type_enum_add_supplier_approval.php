<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Expand enum to include supplier_approval
        DB::statement("ALTER TABLE email_templates MODIFY COLUMN type ENUM('booking','welcome','reset_password','invoice','custom','supplier_approval') DEFAULT 'custom'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE email_templates MODIFY COLUMN type ENUM('booking','welcome','reset_password','invoice','custom') DEFAULT 'custom'");
    }
};

