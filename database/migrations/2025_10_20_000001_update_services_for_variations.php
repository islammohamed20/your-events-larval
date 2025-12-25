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
        Schema::table('services', function (Blueprint $table) {
            // إضافة حقول جديدة للخدمات
            if (! Schema::hasColumn('services', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('services', 'price')) {
                $table->decimal('price', 10, 2)->nullable()->after('description');
            }
            if (! Schema::hasColumn('services', 'service_type')) {
                $table->enum('service_type', ['simple', 'variable'])->default('simple')->after('price');
            }
            if (! Schema::hasColumn('services', 'duration')) {
                $table->integer('duration')->nullable()->after('service_type')->comment('المدة بالساعات');
            }
            if (! Schema::hasColumn('services', 'type')) {
                $table->string('type')->nullable()->after('duration');
            }
            if (! Schema::hasColumn('services', 'features')) {
                $table->json('features')->nullable()->after('type');
            }
            if (! Schema::hasColumn('services', 'custom_fields')) {
                $table->json('custom_fields')->nullable()->after('features');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'price',
                'service_type',
                'duration',
                'type',
                'features',
                'custom_fields',
            ]);
        });
    }
};
