<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('progress_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progress_update_id')->constrained('project_progress_updates');
            $table->string('attachment_type', 10);
            $table->string('url_or_path', 2048);
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->boolean('is_final_result')->default(false);
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_attachments');
    }
};
