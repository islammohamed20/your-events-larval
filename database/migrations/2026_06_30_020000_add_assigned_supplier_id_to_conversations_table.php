<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('assigned_supplier_id')
                ->nullable()
                ->after('assigned_to')
                ->constrained('suppliers')
                ->nullOnDelete();

            $table->index(['assigned_supplier_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropIndex(['assigned_supplier_id', 'status']);
            $table->dropConstrainedForeignId('assigned_supplier_id');
        });
    }
};
