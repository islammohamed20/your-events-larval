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
            $table->boolean('must_change_password')->default(false)->after('status');
            $table->boolean('logout_other_devices')->default(false)->after('must_change_password');
            $table->unsignedInteger('session_version')->default(1)->after('logout_other_devices');
        });

        DB::table('users')->update([
            'must_change_password' => false,
            'logout_other_devices' => false,
            'session_version' => 1,
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['must_change_password', 'logout_other_devices', 'session_version']);
        });
    }
};