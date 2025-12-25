<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // إضافة الحقول المخصصة إلى الخدمات
        Schema::table('services', function (Blueprint $table) {
            if (! Schema::hasColumn('services', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('features');
            }
        });

        // إضافة اختيارات الحقول للسلة
        Schema::table('cart_items', function (Blueprint $table) {
            if (! Schema::hasColumn('cart_items', 'selections')) {
                $table->json('selections')->nullable()->after('customer_notes');
            }
        });

        // إضافة اختيارات الحقول لعناصر عروض الأسعار
        Schema::table('quote_items', function (Blueprint $table) {
            if (! Schema::hasColumn('quote_items', 'selections')) {
                $table->json('selections')->nullable()->after('customer_notes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'custom_fields')) {
                $table->dropColumn('custom_fields');
            }
        });

        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'selections')) {
                $table->dropColumn('selections');
            }
        });

        Schema::table('quote_items', function (Blueprint $table) {
            if (Schema::hasColumn('quote_items', 'selections')) {
                $table->dropColumn('selections');
            }
        });
    }
};
