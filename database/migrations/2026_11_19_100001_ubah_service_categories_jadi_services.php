<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::rename('service_categories', 'services');

        Schema::table('services', function (Blueprint $table) {
            $table->string('short_description', 500)->nullable()->after('slug');
            $table->json('workflow_steps')->nullable()->after('description');
            $table->json('extra_field_schema')->nullable()->after('workflow_steps');
            $table->string('image_path', 2048)->nullable()->after('extra_field_schema');
            $table->unique('name');
        });

        Schema::table('designer_skills', function (Blueprint $table) {
            $table->renameColumn('service_category_id', 'service_id');
        });
    }

    public function down(): void
    {
        Schema::table('designer_skills', function (Blueprint $table) {
            $table->renameColumn('service_id', 'service_category_id');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropColumn(['short_description', 'workflow_steps', 'extra_field_schema', 'image_path']);
        });

        Schema::rename('services', 'service_categories');
    }
};
