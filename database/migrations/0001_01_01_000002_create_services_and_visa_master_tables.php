<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 193+ Countries Master (Scalable beyond 193 countries)
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('iso2', 2)->unique();
            $table->string('iso3', 3)->unique();
            $table->string('currency_code', 3)->default('USD');
            $table->string('embassy_in_qatar_address')->nullable();
            $table->string('official_vac_name')->nullable(); // VFS Global, TLScontact, BLS
            $table->string('official_portal_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Service Categories (Visas, Company Formation, Bank Account, Corporate)
        Schema::create('service_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('code')->unique(); // visa, company_formation, bank_assistance, corporate
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Visa Types (Tourist, Business, Family, Transit, Work, Investment)
        Schema::create('visa_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->string('code')->unique();
            $table->timestamps();
        });

        // Services (Operational Selling Layer)
        Schema::create('services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('category_id')->constrained('service_categories')->cascadeOnDelete();
            $table->foreignUuid('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignUuid('visa_type_id')->nullable()->constrained('visa_types')->nullOnDelete();
            
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            
            // Standard Pricing Structure (Base rates)
            $table->decimal('default_government_fee', 12, 2)->default(0);
            $table->decimal('default_vac_fee', 12, 2)->default(0);
            $table->decimal('default_consultancy_fee', 12, 2)->default(0);
            $table->string('currency', 3)->default('QAR');
            
            $table->integer('standard_sla_days')->default(15);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Visa Master Database: Authoritative Information Layer
        Schema::create('visa_requirements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('country_id')->constrained('countries')->cascadeOnDelete();
            $table->foreignUuid('visa_type_id')->constrained('visa_types')->cascadeOnDelete();
            
            // Official Rules
            $table->boolean('biometrics_required')->default(true);
            $table->boolean('interview_required')->default(false);
            $table->integer('estimated_processing_days')->default(15);
            $table->text('qatar_resident_eligibility')->nullable();
            $table->text('nationality_specific_rules')->nullable();
            
            // Fees
            $table->decimal('official_fee_qar', 12, 2)->default(0);
            $table->decimal('vac_fee_qar', 12, 2)->default(0);
            
            // Verification State (Verified / Needs Confirmation / Do Not Use)
            $table->string('verification_status')->default('verified')->index();
            $table->date('last_verified_date')->nullable();
            $table->date('next_review_date')->nullable();
            
            $table->timestamps();
            $table->unique(['country_id', 'visa_type_id']);
        });

        // Source Evidence & Governance (Section 9: Maker-Checker)
        Schema::create('visa_sources', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('visa_requirement_id')->constrained('visa_requirements')->cascadeOnDelete();
            $table->string('source_type'); // government_portal, embassy_circular, vac_portal
            $table->string('authority_name'); // e.g. UK Home Office, Schengen Consular Authority
            $table->text('source_url');
            $table->string('source_title');
            $table->text('verification_notes')->nullable();
            
            // Governance workflow: Proposer (Research) -> Reviewer (Ops) -> Publisher (Admin/CEO)
            $table->foreignUuid('checked_by_id')->constrained('users');
            $table->date('checked_date');
            $table->foreignUuid('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->string('status')->default('pending_review'); // pending_review, approved, rejected
            $table->timestamps();
        });

        // Source Change Logs (Immutable change tracking for compliance)
        Schema::create('source_change_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('visa_requirement_id')->constrained('visa_requirements')->cascadeOnDelete();
            $table->foreignUuid('changed_by_id')->constrained('users');
            $table->jsonb('old_data')->nullable();
            $table->jsonb('new_data');
            $table->text('change_reason');
            $table->timestamp('created_at');
        });

        // Dynamic Eligibility & Checklist Rules Engine (Section 10)
        Schema::create('service_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('rule_name');
            $table->integer('version')->default(1);
            
            // Condition Schema: {nationality: ['BD', 'IN'], residence: ['resident'], min_bank_statement_months: 6}
            $table->jsonb('eligibility_conditions');
            
            // Checklist Output: {required_documents: [...], conditional_documents: [...], optional: [...]}
            $table->jsonb('document_checklist_rules');
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_rules');
        Schema::dropIfExists('source_change_logs');
        Schema::dropIfExists('visa_sources');
        Schema::dropIfExists('visa_requirements');
        Schema::dropIfExists('services');
        Schema::dropIfExists('visa_types');
        Schema::dropIfExists('service_categories');
        Schema::dropIfExists('countries');
    }
};
