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
        Schema::create('supplier_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المورد
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade'); // الفئة
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade'); // الخدمة
            $table->boolean('is_available')->default(true); // متاح أم لا
            $table->timestamps();
            
            // منع التكرار (كل مورد خدمة واحدة)
            $table->unique(['user_id', 'service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_services');
    }
};
