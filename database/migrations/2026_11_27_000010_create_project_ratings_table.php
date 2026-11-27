<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained('projects');
            $table->foreignId('customer_id')->constrained('users');
            $table->unsignedTinyInteger('rating');
            $table->text('review')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_ratings');
    }
};
