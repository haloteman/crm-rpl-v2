<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 20)->default('unpaid')->after('status_id');
        });

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->timestamp('returning_at')->nullable()->after('converted_at');
        });

        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->foreignId('customer_id')->constrained('users');
            $table->string('payment_type', 20);
            $table->decimal('amount', 15, 2);
            $table->date('paid_at');
            $table->string('method', 20);
            $table->foreignId('recorded_by')->constrained('users');
            $table->string('status', 20);
            $table->text('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->foreignId('replaces_id')->nullable()->constrained('payment_transactions');
            $table->timestamps();

            $table->index(['customer_id', 'paid_at']);
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');

        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropColumn('returning_at');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('payment_status');
        });
    }
};
