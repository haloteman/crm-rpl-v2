<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('designer_id')->constrained('users');
            $table->foreignId('assigned_by')->constrained('users');
            $table->string('assignment_type', 20);
            $table->boolean('is_current')->default(true);
            $table->timestamp('assigned_at');
            $table->timestamp('ended_at')->nullable();
            $table->text('note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_assignments');
    }
};
