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
        // إضافة الأعمدة الناقصة لجدول attributes
        if (!Schema::hasColumn('attributes', 'type')) {
            Schema::table('attributes', function (Blueprint $table) {
                $table->string('type')->default('select')->after('slug');
            });
        }

        if (!Schema::hasColumn('attributes', 'order')) {
            Schema::table('attributes', function (Blueprint $table) {
                $table->integer('order')->default(0)->after('type');
            });
        }

        // إضافة الأعمدة الناقصة لجدول attribute_values
        if (Schema::hasTable('attribute_values')) {
            if (!Schema::hasColumn('attribute_values', 'order')) {
                Schema::table('attribute_values', function (Blueprint $table) {
                    $table->integer('order')->default(0)->after('slug');
                });
            }
        }

        // إضافة عمود order لجدول attribute_service إذا لم يكن موجوداً
        if (Schema::hasTable('attribute_service')) {
            if (!Schema::hasColumn('attribute_service', 'order')) {
                Schema::table('attribute_service', function (Blueprint $table) {
                    $table->integer('order')->default(0)->after('attribute_id');
                });
            }
        }

        // إنشاء جدول service_variations إذا لم يكن موجوداً
        if (!Schema::hasTable('service_variations')) {
            Schema::create('service_variations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('service_id')->constrained()->onDelete('cascade');
                $table->string('sku')->nullable()->unique();
                $table->json('attributes');
                $table->decimal('price', 10, 2);
                $table->decimal('sale_price', 10, 2)->nullable();
                $table->integer('stock')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            // تحديث جدول service_variations الموجود
            Schema::table('service_variations', function (Blueprint $table) {
                if (!Schema::hasColumn('service_variations', 'sku')) {
                    $table->string('sku')->nullable()->unique()->after('service_id');
                }
                if (!Schema::hasColumn('service_variations', 'attributes')) {
                    $table->json('attributes')->after('sku');
                }
                if (!Schema::hasColumn('service_variations', 'sale_price')) {
                    $table->decimal('sale_price', 10, 2)->nullable()->after('price');
                }
                if (!Schema::hasColumn('service_variations', 'stock')) {
                    $table->integer('stock')->nullable()->after('sale_price');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('attributes', 'type')) {
            Schema::table('attributes', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }

        if (Schema::hasColumn('attributes', 'order')) {
            Schema::table('attributes', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }

        if (Schema::hasColumn('attribute_values', 'order')) {
            Schema::table('attribute_values', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }

        if (Schema::hasColumn('attribute_service', 'order')) {
            Schema::table('attribute_service', function (Blueprint $table) {
                $table->dropColumn('order');
            });
        }

        if (Schema::hasTable('service_variations')) {
            if (Schema::hasColumn('service_variations', 'sku')) {
                Schema::table('service_variations', function (Blueprint $table) {
                    $table->dropColumn(['sku', 'sale_price', 'stock']);
                });
            }
            if (Schema::hasColumn('service_variations', 'attributes')) {
                Schema::table('service_variations', function (Blueprint $table) {
                    $table->dropColumn('attributes');
                });
            }
        }
    }
};
