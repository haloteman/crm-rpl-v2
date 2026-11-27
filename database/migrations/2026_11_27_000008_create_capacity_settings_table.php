<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capacity_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('capacity_per_week');
            $table->unsignedTinyInteger('warning_threshold_percent');
            $table->date('effective_from')->unique();
            $table->string('note')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capacity_settings');
    }
};
