<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'booking_date')) {
                $table->date('booking_date')->nullable()->after('selections');
            }
        });

        Schema::table('quote_items', function (Blueprint $table) {
            if (! Schema::hasColumn('quote_items', 'booking_date')) {
                $table->date('booking_date')->nullable()->after('selections');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'booking_date')) {
                $table->dropColumn('booking_date');
            }
        });

        Schema::table('quote_items', function (Blueprint $table) {
            if (Schema::hasColumn('quote_items', 'booking_date')) {
                $table->dropColumn('booking_date');
            }
        });
    }
};
