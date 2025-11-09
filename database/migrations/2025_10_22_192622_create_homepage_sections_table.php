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
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique(); // hero, stats, categories, services, etc.
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('content')->nullable(); // JSON content
            $table->string('image')->nullable();
            $table->string('video_url')->nullable();
            $table->string('background_type')->default('color'); // color, gradient, image
            $table->string('background_value')->nullable(); // color code, gradient, or image path
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->text('settings')->nullable(); // JSON for additional settings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
