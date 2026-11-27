<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->unique()->constrained('projects');
            $table->foreignId('scheme_id')->constrained('profit_share_schemes');
            $table->decimal('gross_revenue', 15, 2);
            $table->decimal('direct_cost_total', 15, 2);
            $table->decimal('net_revenue', 15, 2);
            $table->decimal('rounding_diff', 15, 2);
            $table->timestamp('calculated_at');
            $table->timestamps();
        });

        Schema::create('project_settlement_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('settlement_id')->constrained('project_settlements');
            $table->foreignId('party_id')->constrained('profit_share_parties');
            $table->foreignId('recipient_user_id')->nullable()->constrained('users');
            $table->decimal('percent', 5, 2);
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_settlement_shares');
        Schema::dropIfExists('project_settlements');
    }
};
