<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Document Types Master
        Schema::create('document_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('code')->unique(); // passport, qid, bank_statement, salary_cert, photo, invitation, insurance
            $table->text('description')->nullable();
            $table->boolean('has_expiry_date')->default(false);
            $table->integer('default_validity_months')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Cases: Core Work Unit (Section 11)
        Schema::create('cases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('case_number')->unique(); // e.g. VV-2026-0001
            $table->foreignUuid('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignUuid('country_id')->nullable()->constrained('countries')->nullOnDelete();
            
            // Staff Assignment
            $table->foreignUuid('assigned_officer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignUuid('assigned_manager_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Lifecycle Status (State Machine)
            // Onboarding -> Eligibility -> Doc Requested -> Doc Received -> Doc QC -> App Prep -> App Ready -> App Submitted -> Biometrics -> Under Processing -> Decision -> Passport Return -> Completed
            $table->string('status')->default('onboarding')->index();
            $table->string('priority')->default('standard'); // standard, urgent, vip
            
            // Critical Dates & SLA
            $table->date('intended_travel_date')->nullable();
            $table->date('submission_deadline')->nullable();
            $table->timestamp('sla_due_at')->nullable()->index();
            $table->boolean('is_sla_breached')->default(false);
            
            // Financial State
            $table->string('payment_status')->default('unpaid')->index(); // unpaid, partial, paid
            $table->decimal('total_amount_qar', 12, 2)->default(0);
            $table->decimal('paid_amount_qar', 12, 2)->default(0);
            $table->decimal('balance_qar', 12, 2)->default(0);

            // Rule 11: Immutable Historical Snapshots
            $table->jsonb('checklist_snapshot'); // Frozen snapshot of document requirements
            $table->jsonb('pricing_snapshot');   // Frozen snapshot of rates & fee splits
            $table->jsonb('workflow_snapshot')->nullable();

            $table->text('internal_notes')->nullable(); // Strictly hidden from client
            $table->text('client_notes')->nullable();   // Visible to client
            
            $table->softDeletes();
            $table->timestamps();
        });

        // Case Documents (Section 12: Document Vault & QC Lifecycle)
        Schema::create('case_documents', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->constrained('cases')->cascadeOnDelete();
            $table->foreignUuid('document_type_id')->constrained('document_types')->cascadeOnDelete();
            
            $table->string('title');
            $table->string('file_path'); // MinIO S3 object key
            $table->string('file_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('file_size');
            
            // Versioning
            $table->integer('version')->default(1);
            $table->string('uploaded_by_type')->default('staff'); // staff, client, b2b_partner
            $table->uuid('uploaded_by_id')->nullable();
            
            // QC Lifecycle: requested -> uploaded -> under_review -> verified -> rejected -> expired
            $table->string('status')->default('requested')->index();
            $table->text('rejection_reason')->nullable();
            
            // Expiry tracking
            $table->date('expiry_date')->nullable()->index();
            $table->boolean('is_expiry_alert_sent')->default(false);
            
            // Verification audit
            $table->foreignUuid('verified_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
        });

        // Document Versions: Never overwrite! (Rule 11)
        Schema::create('document_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_document_id')->constrained('case_documents')->cascadeOnDelete();
            $table->integer('version_number');
            $table->string('file_path');
            $table->string('file_name');
            $table->unsignedBigInteger('file_size');
            $table->string('uploaded_by_type');
            $table->uuid('uploaded_by_id')->nullable();
            $table->string('qc_status')->default('pending');
            $table->text('qc_comment')->nullable();
            $table->timestamps();
        });

        // Official Embassy / Authority Applications
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->constrained('cases')->cascadeOnDelete();
            $table->string('application_reference')->nullable()->index();
            $table->string('portal_name')->nullable(); // VFS, TLS, MOI Qatar, CEOP
            $table->date('submission_date')->nullable();
            $table->string('decision')->nullable(); // approved, refused, withdrawn
            $table->date('decision_date')->nullable();
            $table->text('decision_remarks')->nullable();
            $table->timestamps();
        });

        // Biometric / VAC / Embassy Appointments
        Schema::create('appointments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->constrained('cases')->cascadeOnDelete();
            $table->string('appointment_type'); // biometrics, document_submission, interview, medical
            $table->string('location_name');
            $table->dateTime('appointment_time');
            $table->string('status')->default('scheduled'); // scheduled, attended, missed, rescheduled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Tasks & Follow-ups (Section 13)
        Schema::create('tasks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->nullable()->constrained('cases')->cascadeOnDelete();
            $table->foreignUuid('assigned_to_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('created_by_id')->constrained('users')->cascadeOnDelete();
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium'); // low, medium, high, urgent
            $table->string('status')->default('pending')->index(); // pending, in_progress, completed, overdue, cancelled
            
            $table->dateTime('due_at')->index();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });

        // SLA Rules & Events
        Schema::create('sla_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_id')->nullable()->constrained('services')->cascadeOnDelete();
            $table->string('stage'); // document_qc, application_prep, submission, decision
            $table->integer('max_hours');
            $table->integer('warning_hours');
            $table->timestamps();
        });

        Schema::create('sla_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->constrained('cases')->cascadeOnDelete();
            $table->foreignUuid('sla_rule_id')->constrained('sla_rules')->cascadeOnDelete();
            $table->string('event_type'); // warning, breach, resolved
            $table->dateTime('triggered_at');
            $table->jsonb('event_details')->nullable();
            $table->timestamps();
        });

        // Timeline Events: Complete 360 Case Story
        Schema::create('timeline_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->constrained('cases')->cascadeOnDelete();
            $table->foreignUuid('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type'); // status_change, document_upload, qc_verified, qc_rejected, payment_received, note_added
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_client_visible')->default(true);
            $table->jsonb('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timeline_events');
        Schema::dropIfExists('sla_events');
        Schema::dropIfExists('sla_rules');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('appointments');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('case_documents');
        Schema::dropIfExists('cases');
        Schema::dropIfExists('document_types');
    }
};
