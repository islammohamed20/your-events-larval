<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update existing services to set service_type based on their attributes
     */
    public function up(): void
    {
        // Update services that have attributes to 'variable'
        DB::statement("
            UPDATE services 
            SET service_type = 'variable' 
            WHERE id IN (
                SELECT DISTINCT service_id 
                FROM attribute_service
            )
            AND (service_type IS NULL OR service_type = 'simple')
        ");

        // Update remaining services to 'simple'
        DB::statement("
            UPDATE services 
            SET service_type = 'simple' 
            WHERE service_type IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse - service_type is still needed
    }
};
