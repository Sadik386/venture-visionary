<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // B2B Partners (Section 15: Partner Isolation)
        Schema::create('partners', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('partner_code')->unique(); // e.g. PTR-2026-0001
            $table->string('company_name');
            $table->string('trade_license_number')->nullable()->index();
            $table->string('country');
            $table->string('city')->nullable();
            
            // Primary Contact
            $table->string('contact_person');
            $table->string('phone')->index();
            $table->string('email')->unique();
            
            // Lifecycle: applied -> under_review -> approved -> active -> suspended -> blocked
            $table->string('status')->default('applied')->index();
            
            // Commercial Terms
            $table->decimal('credit_limit_qar', 12, 2)->default(0);
            $table->integer('payment_terms_days')->default(15);
            $table->date('agreement_expiry_date')->nullable();
            
            $table->foreignUuid('account_manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });

        // B2B Partner Authorized Users
        Schema::create('partner_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('role')->default('staff'); // admin, agent, accountant
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        // Partner-Specific Net Rates (Historical rate locks per Section 15)
        Schema::create('partner_pricing', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            
            $table->decimal('partner_net_rate', 12, 2);
            $table->string('currency', 3)->default('QAR');
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->foreignUuid('agreed_by_id')->constrained('users');
            $table->timestamps();
        });

        // Partner Wallets (Prepaid balance and credit limits)
        Schema::create('partner_wallets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('partner_id')->unique()->constrained('partners')->cascadeOnDelete();
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->string('currency', 3)->default('QAR');
            $table->timestamps();
        });

        // Partner Wallet Transactions Ledger
        Schema::create('partner_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('partner_wallet_id')->constrained('partner_wallets')->cascadeOnDelete();
            $table->string('transaction_type'); // deposit, deduction, refund, credit_adjustment
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->string('reference_type')->nullable(); // Case or Invoice
            $table->uuid('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignUuid('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Partner Agreements
        Schema::create('partner_agreements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->string('agreement_title');
            $table->string('document_file_path');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('status')->default('active'); // active, expired, terminated
            $table->timestamps();
        });

        // Partner Service Whitelist
        Schema::create('partner_service_access', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('partner_id')->constrained('partners')->cascadeOnDelete();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            $table->boolean('is_permitted')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_service_access');
        Schema::dropIfExists('partner_agreements');
        Schema::dropIfExists('partner_transactions');
        Schema::dropIfExists('partner_wallets');
        Schema::dropIfExists('partner_pricing');
        Schema::dropIfExists('partner_users');
        Schema::dropIfExists('partners');
    }
};
