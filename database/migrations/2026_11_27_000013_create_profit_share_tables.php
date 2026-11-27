<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profit_share_parties', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('label', 100);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('profit_share_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('label', 100);
            $table->date('effective_from');
            $table->boolean('is_active')->default(true);
            $table->decimal('operating_cash_percent', 5, 2)->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('profit_share_scheme_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scheme_id')->constrained('profit_share_schemes');
            $table->foreignId('party_id')->constrained('profit_share_parties');
            $table->decimal('percent', 5, 2);
            $table->timestamps();

            $table->unique(['scheme_id', 'party_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profit_share_scheme_items');
        Schema::dropIfExists('profit_share_schemes');
        Schema::dropIfExists('profit_share_parties');
    }
};
