<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('event_lat', 10, 7)->nullable()->after('event_location');
            $table->decimal('event_lng', 10, 7)->nullable()->after('event_lat');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['event_lat', 'event_lng']);
        });
    }
};
