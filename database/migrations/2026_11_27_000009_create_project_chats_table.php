<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_chats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained('projects');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('project_chats');
            $table->foreignId('user_id')->constrained('users');
            $table->string('participant_role', 20);
            $table->timestamp('joined_at');
            $table->timestamp('left_at')->nullable();
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();

            $table->unique(['chat_id', 'user_id']);
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('project_chats');
            $table->foreignId('sender_id')->constrained('users');
            $table->string('message_type', 20);
            $table->text('body');
            $table->timestamps();

            $table->index(['chat_id', 'created_at']);
        });

        Schema::create('chat_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_messages');
            $table->string('file_path', 2048);
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100);
            $table->unsignedInteger('file_size');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_attachments');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_participants');
        Schema::dropIfExists('project_chats');
    }
};
