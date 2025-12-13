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
        DB::statement("
            ALTER TABLE otp_verifications
            MODIFY COLUMN type ENUM(
                'email_verification',
                'login',
                'password_reset',
                'booking_confirmation',
                'payment_confirmation',
                'supplier_login'
            ) DEFAULT 'email_verification'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE otp_verifications
            MODIFY COLUMN type ENUM(
                'email_verification',
                'login',
                'password_reset',
                'booking_confirmation',
                'payment_confirmation'
            ) DEFAULT 'email_verification'
        ");
    }
};
