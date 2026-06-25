<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('two_factor_enabled')->default(false)->after('phone');
            $table->enum('two_factor_channel', ['email', 'whatsapp'])->default('email')->after('two_factor_enabled');
        });

        Schema::table('otp_verifications', function (Blueprint $table) {
            $table->string('channel', 20)->default('email')->after('user_agent');
            $table->string('phone', 20)->nullable()->after('channel');
        });

        DB::statement("
            ALTER TABLE otp_verifications
            MODIFY COLUMN type ENUM(
                'email_verification',
                'login',
                'password_reset',
                'booking_confirmation',
                'payment_confirmation',
                'supplier_login',
                'two_factor'
            ) DEFAULT 'email_verification'
        ");
    }

    public function down(): void
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

        Schema::table('otp_verifications', function (Blueprint $table) {
            if (Schema::hasColumn('otp_verifications', 'channel')) {
                $table->dropColumn('channel');
            }
            if (Schema::hasColumn('otp_verifications', 'phone')) {
                $table->dropColumn('phone');
            }
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_enabled', 'two_factor_channel']);
        });
    }
};
