<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_pricings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders');
            $table->decimal('final_price', 15, 2);
            $table->string('dp_type', 20);
            $table->decimal('dp_value', 15, 2);
            $table->decimal('dp_amount', 15, 2);
            $table->foreignId('set_by')->constrained('users');
            $table->timestamp('set_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_pricings');
    }
};
