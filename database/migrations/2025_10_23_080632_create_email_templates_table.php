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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم القالب
            $table->string('slug')->unique(); // معرف فريد
            $table->string('subject'); // موضوع الإيميل
            $table->text('body'); // محتوى الإيميل (HTML)
            $table->json('variables')->nullable(); // المتغيرات المتاحة
            $table->enum('type', ['booking', 'welcome', 'reset_password', 'invoice', 'custom'])->default('custom');
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index('slug');
            $table->index('type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
