<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users');
            $table->string('phone', 20);
            $table->string('institution')->nullable();
            $table->string('status', 20);
            $table->string('lead_source', 30);
            $table->timestamp('registered_at');
            $table->timestamp('converted_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
