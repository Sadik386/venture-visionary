<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflows', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('trigger_event')->index();
            $table->text('description')->nullable();
            $table->jsonb('conditions')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('execution_count')->default(0);
            $table->dateTime('last_triggered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('workflow_actions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workflow_id')->constrained('workflows')->cascadeOnDelete();
            $table->string('action_type');
            $table->jsonb('action_config')->nullable();
            $table->integer('order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['workflow_id', 'order']);
        });

        Schema::create('workflow_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('workflow_id')->constrained('workflows')->cascadeOnDelete();
            $table->string('trigger_event');
            $table->string('entity_type')->nullable();
            $table->string('entity_id')->nullable();
            $table->string('status')->default('success')->index(); // success, failed, skipped
            $table->jsonb('details')->nullable();
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_logs');
        Schema::dropIfExists('workflow_actions');
        Schema::dropIfExists('workflows');
    }
};
