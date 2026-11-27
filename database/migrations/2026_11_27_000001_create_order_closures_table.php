<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // data pembatalan dipindah ke order_closures
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('cancelled_by');
            $table->dropColumn(['cancel_reason', 'cancelled_at']);
            $table->index(['expected_deadline', 'status_id']);
        });

        Schema::create('closure_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('closure_type', 20);
            $table->string('code', 30);
            $table->string('label', 100);
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['closure_type', 'code']);
        });

        Schema::create('order_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders');
            $table->string('closure_type', 20);
            $table->foreignId('reason_id')->nullable()->constrained('closure_reasons');
            $table->text('reason_note');
            $table->foreignId('closed_by')->constrained('users');
            $table->timestamp('closed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_closures');
        Schema::dropIfExists('closure_reasons');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['expected_deadline', 'status_id']);
            $table->text('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
        });
    }
};
