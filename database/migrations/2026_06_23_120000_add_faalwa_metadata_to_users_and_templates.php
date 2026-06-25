<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'faalwa_agent_id')) {
                $table->unsignedBigInteger('faalwa_agent_id')->nullable()->after('card_expiry_year');
            }
        });

        Schema::table('message_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('message_templates', 'faalwa_namespace')) {
                $table->string('faalwa_namespace')->nullable()->after('type');
            }

            if (! Schema::hasColumn('message_templates', 'language_code')) {
                $table->string('language_code', 10)->default('ar')->after('faalwa_namespace');
            }

            if (! Schema::hasColumn('message_templates', 'params_schema')) {
                $table->json('params_schema')->nullable()->after('language_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'faalwa_agent_id')) {
                $table->dropColumn('faalwa_agent_id');
            }
        });

        Schema::table('message_templates', function (Blueprint $table) {
            $drops = [];
            foreach (['faalwa_namespace', 'language_code', 'params_schema'] as $column) {
                if (Schema::hasColumn('message_templates', $column)) {
                    $drops[] = $column;
                }
            }

            if ($drops) {
                $table->dropColumn($drops);
            }
        });
    }
};