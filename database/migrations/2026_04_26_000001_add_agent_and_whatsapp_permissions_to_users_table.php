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
            if (! Schema::hasColumn('users', 'is_online')) {
                $table->boolean('is_online')->default(false)->after('role');
            }
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user', 'agent') NOT NULL DEFAULT 'user'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'user') NOT NULL DEFAULT 'user'");

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_online')) {
                $table->dropColumn('is_online');
            }
        });
    }
};