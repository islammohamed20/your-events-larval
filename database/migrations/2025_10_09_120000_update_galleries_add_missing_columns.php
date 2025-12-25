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
        // If the legacy singular table was accidentally created, we ignore it here.
        if (Schema::hasTable('galleries')) {
            Schema::table('galleries', function (Blueprint $table) {
                if (! Schema::hasColumn('galleries', 'file_path')) {
                    $table->string('file_path')->nullable()->after('path');
                }
                if (! Schema::hasColumn('galleries', 'category')) {
                    $table->string('category')->nullable()->after('type');
                }
                if (! Schema::hasColumn('galleries', 'file_size')) {
                    $table->bigInteger('file_size')->nullable()->after('is_featured');
                }
                if (! Schema::hasColumn('galleries', 'mime_type')) {
                    $table->string('mime_type')->nullable()->after('file_size');
                }
                if (! Schema::hasColumn('galleries', 'alt_text')) {
                    $table->string('alt_text')->nullable()->after('mime_type');
                }
                if (! Schema::hasColumn('galleries', 'sort_order')) {
                    $table->integer('sort_order')->default(0)->after('alt_text');
                }
            });

            // Indexes (wrapped in try/catch to avoid errors if they exist already)
            try {
                DB::statement('CREATE INDEX galleries_sort_order_index ON galleries (sort_order)');
            } catch (\Throwable $e) {
            }
            try {
                DB::statement('CREATE INDEX galleries_category_index ON galleries (category)');
            } catch (\Throwable $e) {
            }

            // Populate file_path from legacy path column if needed
            if (Schema::hasColumn('galleries', 'path')) {
                DB::table('galleries')
                    ->whereNull('file_path')
                    ->whereNotNull('path')
                    ->update(['file_path' => DB::raw('path')]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't drop columns to avoid data loss; optionally implement if needed.
    }
};
