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
        Schema::table('supplier_services', function (Blueprint $table) {
            // Drop foreign key and unique constraint
            // Note: The index name might be automatically generated.
            // We'll try to drop using array syntax which Laravel resolves to the index name.
            
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'service_id']);
            
            // Rename column
            $table->renameColumn('user_id', 'supplier_id');
        });

        Schema::table('supplier_services', function (Blueprint $table) {
            // Add new foreign key and unique constraint
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->unique(['supplier_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('supplier_services', function (Blueprint $table) {
             $table->dropForeign(['supplier_id']);
             $table->dropUnique(['supplier_id', 'service_id']);
             
             $table->renameColumn('supplier_id', 'user_id');
        });

        Schema::table('supplier_services', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'service_id']);
        });
    }
};
