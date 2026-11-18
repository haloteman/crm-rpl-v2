<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('scope', 20);
            $table->string('code', 30);
            $table->string('label', 100);
            $table->string('customer_label', 100);
            $table->integer('sort_order')->default(0);
            $table->json('allowed_next')->nullable();
            $table->boolean('is_final')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['scope', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_definitions');
    }
};
