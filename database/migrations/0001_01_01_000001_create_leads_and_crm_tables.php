<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lead Sources (Website, Meta Lead Ads, WhatsApp, Referral, B2B, Walk-in)
        Schema::create('lead_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Leads (Section 6: Lead Management)
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('lead_number')->unique(); // e.g. LD-2026-0001
            $table->string('name');
            $table->string('phone')->nullable()->index();
            $table->string('whatsapp')->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('nationality')->nullable()->index();
            $table->string('qatar_residence_status')->nullable(); // citizen, resident, visit_visa, outside_qatar
            $table->string('destination_country')->nullable();
            $table->string('visa_or_service_type')->nullable();
            $table->date('planned_travel_date')->nullable();
            
            $table->foreignUuid('source_id')->nullable()->constrained('lead_sources')->nullOnDelete();
            $table->string('campaign')->nullable();
            $table->foreignUuid('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            
            // Pipeline: New -> Contacted -> Qualified -> Consultation -> Quotation Sent -> Follow-up -> Payment Pending -> Converted / Lost / Duplicate
            $table->string('status')->default('new')->index();
            $table->integer('lead_score')->default(50);
            
            // Duplicate Warning Flags (Checked before record creation)
            $table->string('passport_no')->nullable()->index();
            $table->string('qid_no')->nullable()->index();
            $table->boolean('is_duplicate')->default(false);
            $table->uuid('duplicate_of_id')->nullable();

            $table->timestamp('last_contacted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        // Lead Activities (Calls, WhatsApp messages, Meeting notes, Status transitions)
        Schema::create('lead_activities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('lead_id')->constrained('leads')->cascadeOnDelete();
            $table->foreignUuid('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type'); // call, whatsapp, email, consultation, status_change, note
            $table->string('title');
            $table->text('details')->nullable();
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
        });

        // Lead Assignments (Supports Round Robin, country/service based, and manual assignment)
        Schema::create('assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('assignable_type'); // Lead or Case
            $table->uuid('assignable_id');
            $table->foreignUuid('assigned_from_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('assigned_to_id')->constrained('users')->cascadeOnDelete();
            $table->string('rule_applied')->default('manual'); // round_robin, country_specialist, manual
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['assignable_type', 'assignable_id']);
        });

        // Clients (Section 7: Client 360 - Strictly Separate from Case)
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('client_code')->unique(); // e.g. CL-2026-0001
            $table->string('full_name');
            $table->string('passport_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->index();
            
            // Sensitive fields with field-level access control
            $table->string('passport_number')->nullable()->index();
            $table->date('passport_expiry_date')->nullable()->index();
            $table->string('qid_number')->nullable()->index();
            $table->date('qid_expiry_date')->nullable()->index();
            $table->string('qatar_residence_status')->nullable();
            $table->text('qatar_address')->nullable();
            
            $table->string('phone')->index();
            $table->string('whatsapp')->nullable()->index();
            $table->string('email')->nullable()->index();
            
            // Client Travel & Business Profile
            $table->jsonb('travel_history')->nullable(); // previous visas, refusals, stamps
            $table->jsonb('business_profile')->nullable(); // company ownership, employment status
            
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        // Client Contacts (Family members, emergency contacts, corporate liaisons)
        Schema::create('client_contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('name');
            $table->string('relationship'); // spouse, child, sponsor, manager
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        // Client Master Documents (Vault of reusable identity proofs)
        Schema::create('client_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('document_type'); // passport_bio, qid_front, qid_back, photo, trade_license
            $table->string('file_name');
            $table->string('file_path'); // S3/MinIO private object key
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            $table->date('expiry_date')->nullable()->index();
            $table->boolean('is_verified')->default(false);
            $table->foreignUuid('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_documents');
        Schema::dropIfExists('client_contacts');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('lead_activities');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('lead_sources');
    }
};
