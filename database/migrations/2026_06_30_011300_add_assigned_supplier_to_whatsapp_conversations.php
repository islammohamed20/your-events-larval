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
        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->foreignId('assigned_supplier_id')->nullable()->after('supplier_id')->constrained('suppliers')->onDelete('set null');
            $table->index('assigned_supplier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->dropForeign(['assigned_supplier_id']);
            $table->dropIndex(['assigned_supplier_id']);
            $table->dropColumn('assigned_supplier_id');
        });
    }
};
