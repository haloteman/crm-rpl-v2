<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_pricing_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->decimal('old_final_price', 15, 2)->nullable();
            $table->string('old_dp_type', 20)->nullable();
            $table->decimal('old_dp_value', 15, 2)->nullable();
            $table->decimal('new_final_price', 15, 2);
            $table->string('new_dp_type', 20);
            $table->decimal('new_dp_value', 15, 2);
            $table->foreignId('changed_by')->constrained('users');
            $table->timestamp('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_pricing_histories');
    }
};
