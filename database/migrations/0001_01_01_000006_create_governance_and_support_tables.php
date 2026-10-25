<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Omnichannel Conversations (Section 17: WhatsApp & Communications)
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('channel'); // whatsapp, facebook, instagram, website, email
            $table->string('channel_chat_id')->index(); // External conversation or phone number
            $table->foreignUuid('lead_id')->nullable()->constrained('leads')->nullOnDelete();
            $table->foreignUuid('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignUuid('case_id')->nullable()->constrained('cases')->nullOnDelete();
            $table->foreignUuid('assigned_staff_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('open'); // open, pending, resolved
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        // Messages
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->string('direction'); // inbound, outbound
            $table->string('sender_type'); // staff, customer, bot, system
            $table->uuid('sender_id')->nullable();
            $table->text('body')->nullable();
            $table->string('media_url')->nullable();
            $table->string('media_type')->nullable(); // image, document, audio, video
            $table->string('delivery_status')->default('sent'); // sent, delivered, read, failed
            $table->string('external_message_id')->nullable()->index();
            $table->jsonb('raw_payload')->nullable();
            $table->timestamps();
        });

        // Message Templates (WhatsApp & Transactional Email)
        Schema::create('message_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('channel')->default('whatsapp'); // whatsapp, email, sms
            $table->string('category'); // utility, marketing, service
            $table->string('language', 10)->default('en');
            $table->text('template_body');
            $table->jsonb('variable_mapping')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Notification Templates (Section 18: Notification & Automation Engine)
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('trigger_event')->unique(); // new_lead, qc_rejection, document_expiry, sla_warning, etc.
            $table->string('title');
            $table->text('in_app_message');
            $table->text('whatsapp_message')->nullable();
            $table->text('email_subject')->nullable();
            $table->text('email_body')->nullable();
            $table->boolean('send_in_app')->default(true);
            $table->boolean('send_whatsapp')->default(true);
            $table->boolean('send_email')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Notification Logs
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('channel'); // in_app, whatsapp, email, sms
            $table->string('recipient');
            $table->string('title');
            $table->text('content');
            $table->string('status')->default('sent'); // sent, failed
            $table->text('error_details')->nullable();
            $table->timestamp('sent_at');
        });

        // Support Tickets
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ticket_number')->unique();
            $table->string('creator_type'); // client, b2b_partner, staff
            $table->uuid('creator_id');
            $table->foreignUuid('case_id')->nullable()->constrained('cases')->nullOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->string('priority')->default('normal'); // low, normal, urgent
            $table->string('status')->default('open'); // open, in_progress, waiting_client, closed
            $table->foreignUuid('assigned_to_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Audit Logs (Section 4 & 22: Rule 3 Compliance)
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // created, updated, deleted, approved, rejected, status_changed
            $table->string('auditable_type'); // Model class
            $table->uuid('auditable_id');
            $table->jsonb('old_values')->nullable();
            $table->jsonb('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['auditable_type', 'auditable_id']);
        });

        // Idempotent Webhook Events (Section 24)
        Schema::create('webhook_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('source'); // meta, whatsapp, payment_gateway, website
            $table->string('event_id')->unique(); // Prevents duplicate processing
            $table->string('event_type');
            $table->jsonb('payload');
            $table->string('status')->default('pending'); // pending, processed, failed
            $table->integer('retry_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // Dynamic System Settings (No-Code Configuration - Section 23)
        Schema::create('system_settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->string('group')->default('general'); // general, localization, security, sla
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
        Schema::dropIfExists('webhook_events');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('message_templates');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('conversations');
    }
};
