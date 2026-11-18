<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 30)->unique();
            $table->foreignId('customer_id')->constrained('users');
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('status_id')->constrained('status_definitions');
            $table->foreignId('created_by')->constrained('users');
            $table->string('created_via', 20);
            $table->foreignId('reorder_from_id')->nullable()->constrained('orders');
            $table->string('title');
            $table->text('description');
            $table->date('expected_deadline');
            $table->decimal('budget_estimate', 15, 2)->nullable();
            $table->json('extra_fields')->nullable();
            $table->string('contact_name');
            $table->string('contact_phone', 20);
            $table->text('cancel_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('order_references', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->string('reference_type', 10);
            $table->string('url_or_path', 2048);
            $table->string('original_name')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders');
            $table->string('from_status', 30)->nullable();
            $table->string('to_status', 30);
            $table->foreignId('changed_by')->nullable()->constrained('users');
            $table->text('note')->nullable();
            $table->timestamp('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_references');
        Schema::dropIfExists('orders');
    }
};
