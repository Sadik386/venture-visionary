<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Dynamic Pricing Rules
        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('service_id')->constrained('services')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('min_rate', 12, 2);
            $table->decimal('standard_rate', 12, 2);
            $table->string('currency', 3)->default('QAR');
            $table->jsonb('criteria')->nullable(); // e.g. urgent, vip, family discount
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Quotations (Section 16)
        Schema::create('quotations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('quotation_number')->unique(); // e.g. QT-2026-0001
            $table->foreignUuid('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->uuid('partner_id')->nullable(); // For B2B partner quotes
            $table->foreignUuid('case_id')->nullable()->constrained('cases')->nullOnDelete();
            
            // Fee breakdown
            $table->decimal('government_fee', 12, 2)->default(0);
            $table->decimal('vac_fee', 12, 2)->default(0);
            $table->decimal('consultancy_fee', 12, 2)->default(0);
            $table->decimal('other_charges', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            
            // Multi-Currency Snapshot
            $table->string('currency', 3)->default('QAR');
            $table->decimal('exchange_rate_to_qar', 10, 4)->default(1.0000);
            
            $table->date('valid_until');
            $table->string('status')->default('draft'); // draft, sent, accepted, rejected, expired
            
            $table->foreignUuid('created_by_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Quotation Line Items
        Schema::create('quotation_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('quotation_id')->constrained('quotations')->cascadeOnDelete();
            $table->string('item_name');
            $table->string('fee_type'); // government, vac, consultancy, translation, courier
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
        });

        // Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('invoice_number')->unique(); // e.g. INV-2026-0001
            $table->foreignUuid('case_id')->nullable()->constrained('cases')->nullOnDelete();
            $table->foreignUuid('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->uuid('partner_id')->nullable();
            
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0);
            $table->decimal('balance_due', 12, 2);
            
            $table->string('currency', 3)->default('QAR');
            $table->decimal('exchange_rate_to_qar', 10, 4)->default(1.0000);
            
            $table->date('issue_date');
            $table->date('due_date');
            $table->string('status')->default('sent')->index(); // draft, sent, partial, paid, cancelled, overdue
            
            $table->foreignUuid('created_by_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });

        // Invoice Items
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('description');
            $table->string('fee_category'); // government, service, translation, courier
            $table->integer('quantity')->default(1);
            $table->decimal('unit_amount', 12, 2);
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();
        });

        // Payments & Receipts
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->foreignUuid('case_id')->nullable()->constrained('cases')->nullOnDelete();
            $table->string('receipt_number')->unique(); // e.g. RCP-2026-0001
            
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('QAR');
            $table->decimal('exchange_rate_to_qar', 10, 4)->default(1.0000);
            $table->decimal('amount_in_qar', 12, 2);
            
            $table->string('payment_method'); // card, cash, bank_transfer, partner_wallet
            $table->string('transaction_reference')->nullable()->index();
            $table->date('payment_date');
            
            $table->foreignUuid('received_by_id')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Direct Case Expenses (Enables accurate Gross Margin calculation per case)
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('case_id')->nullable()->constrained('cases')->nullOnDelete();
            $table->string('expense_category'); // embassy_fee, typing_center, translation, courier, medical
            $table->string('vendor_name')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 3)->default('QAR');
            $table->string('receipt_file_path')->nullable();
            $table->date('expense_date');
            $table->foreignUuid('recorded_by_id')->constrained('users');
            $table->timestamps();
        });

        // Refunds (Dual-authorization governance)
        Schema::create('refunds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('payment_id')->constrained('payments')->cascadeOnDelete();
            $table->foreignUuid('case_id')->nullable()->constrained('cases')->nullOnDelete();
            $table->decimal('refund_amount', 12, 2);
            $table->string('currency', 3)->default('QAR');
            $table->text('reason');
            
            // Dual Authorization
            $table->foreignUuid('requested_by_id')->constrained('users');
            $table->foreignUuid('approved_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->string('status')->default('pending'); // pending, approved, rejected, processed
            $table->string('refund_transaction_ref')->nullable();
            $table->timestamps();
        });

        // Credit Notes
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('credit_note_number')->unique();
            $table->foreignUuid('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->text('reason');
            $table->foreignUuid('issued_by_id')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('quotation_items');
        Schema::dropIfExists('quotations');
        Schema::dropIfExists('pricing_rules');
    }
};
