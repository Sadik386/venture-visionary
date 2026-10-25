<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('token')->unique()->index();
            $table->foreignUuid('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignUuid('case_id')->nullable()->constrained('cases')->nullOnDelete();
            $table->foreignUuid('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->string('recipient_name');
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_email')->nullable();
            $table->string('channel')->default('whatsapp'); // whatsapp, sms, email
            $table->string('status')->default('sent')->index(); // sent, opened, completed, declined
            $table->smallInteger('rating')->nullable()->index(); // 1-5
            $table->text('feedback')->nullable();
            $table->boolean('public_review_redirected')->default(false);
            $table->string('google_review_url')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('review_requests');
    }
};
