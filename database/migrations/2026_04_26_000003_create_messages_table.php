<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete()->index();
            $table->enum('sender_type', ['agent', 'customer']);
            $table->text('message');
            $table->enum('message_type', ['text', 'template'])->default('text');
            $table->string('external_id')->nullable()->index();
            $table->enum('status', ['sent', 'delivered', 'read', 'failed'])->default('sent')->index();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};