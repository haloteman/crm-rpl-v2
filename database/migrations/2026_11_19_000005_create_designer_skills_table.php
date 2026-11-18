<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('designer_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('service_category_id')->constrained('service_categories');
            $table->string('proficiency', 20)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'service_category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('designer_skills');
    }
};
