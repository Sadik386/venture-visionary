<?php

namespace Tests\Feature;

use App\Models\CaseDocument;
use App\Models\Client;
use App\Models\Country;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\TimelineEvent;
use App\Models\User;
use App\Models\VisaCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationsAndCommercialTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_quotation_pdf_export_downloads_valid_pdf()
    {
        $client = Client::firstOrCreate(
            ['phone' => '+97455551111'],
            ['full_name' => 'Test Diplomatic Client', 'nationality' => 'Qatar', 'email' => 'client@test.com']
        );

        $quotation = Quotation::create([
            'client_id' => $client->id,
            'government_fee' => 350.00,
            'vac_fee' => 120.00,
            'consultancy_fee' => 1500.00,
            'other_charges' => 0.00,
            'discount_amount' => 100.00,
            'total_amount' => 1870.00,
            'currency' => 'QAR',
            'status' => 'draft',
            'valid_until' => now()->addDays(14),
        ]);

        $response = $this->get(route('quotations.pdf', $quotation));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_invoice_pdf_export_downloads_valid_pdf()
    {
        $client = Client::firstOrCreate(
            ['phone' => '+97455552222'],
            ['full_name' => 'Corporate Billing Client', 'nationality' => 'Qatar', 'email' => 'corp@test.com']
        );

        $invoice = Invoice::create([
            'client_id' => $client->id,
            'subtotal' => 2000.00,
            'tax_amount' => 0.00,
            'discount_amount' => 0.00,
            'total_amount' => 2000.00,
            'paid_amount' => 500.00,
            'balance_due' => 1500.00,
            'currency' => 'QAR',
            'status' => 'partial',
            'issue_date' => now(),
            'due_date' => now()->addDays(30),
            'line_items' => [
                ['description' => 'Schengen Business Visa Consultation', 'unit_price' => 2000.00, 'quantity' => 1, 'total' => 2000.00]
            ],
        ]);

        $response = $this->get(route('invoices.pdf', $invoice));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_document_qc_approval_and_rejection_lifecycle()
    {
        $user = User::first() ?? User::factory()->create(['email' => 'officer@test.com']);
        $client = Client::first();
        $country = Country::first();
        $service = Service::first();

        $case = VisaCase::create([
            'client_id' => $client->id,
            'country_id' => $country->id,
            'service_id' => $service->id,
            'status' => 'documents_pending',
        ]);

        $doc = CaseDocument::create([
            'case_id' => $case->id,
            'title' => 'Passport Biodata Page',
            'file_path' => 'case-documents/passport.pdf',
            'file_name' => 'passport.pdf',
            'status' => 'uploaded',
            'uploaded_by_type' => 'client',
            'uploaded_by_id' => (string) $user->id,
            'version' => 1,
        ]);

        // QC Approval
        $doc->update([
            'status' => 'verified',
            'verified_by_id' => $user->id,
            'verified_at' => now(),
        ]);

        $this->assertEquals('verified', $doc->fresh()->status);
        $this->assertEquals($user->id, $doc->fresh()->verified_by_id);

        // QC Rejection with feedback
        $doc->update([
            'status' => 'rejected',
            'rejection_reason' => 'Passport validity is less than 6 months required by embassy.',
        ]);

        $this->assertEquals('rejected', $doc->fresh()->status);
        $this->assertStringContainsString('6 months', $doc->fresh()->rejection_reason);
    }

    public function test_payment_recording_updates_invoice_and_case_balance()
    {
        $user = User::first() ?? User::factory()->create();
        $client = Client::first();
        $country = Country::first();
        $service = Service::first();

        $case = VisaCase::create([
            'client_id' => $client->id,
            'country_id' => $country->id,
            'service_id' => $service->id,
            'total_amount_qar' => 3000.00,
            'paid_amount_qar' => 0.00,
            'balance_qar' => 3000.00,
            'status' => 'inquiry',
        ]);

        $invoice = Invoice::create([
            'client_id' => $client->id,
            'case_id' => $case->id,
            'subtotal' => 3000.00,
            'total_amount' => 3000.00,
            'paid_amount' => 0.00,
            'balance_due' => 3000.00,
            'currency' => 'QAR',
            'status' => 'sent',
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
        ]);

        // Record a partial payment of 1000 QAR
        Payment::create([
            'invoice_id' => $invoice->id,
            'case_id' => $case->id,
            'receipt_number' => 'RCP-' . strtoupper(substr(uniqid(), -6)),
            'amount' => 1000.00,
            'currency' => 'QAR',
            'exchange_rate_to_qar' => 1.0,
            'amount_in_qar' => 1000.00,
            'payment_method' => 'bank_transfer',
            'transaction_reference' => 'QNB-TRX-12345',
            'payment_date' => now(),
            'received_by_id' => $user->id,
        ]);

        $invoice->update([
            'paid_amount' => 1000.00,
            'balance_due' => 2000.00,
            'status' => 'partial',
        ]);

        $case->increment('paid_amount_qar', 1000.00);
        $case->balance_qar = max(0, 3000.00 - 1000.00);
        $case->save();

        $this->assertEquals(1000.00, (float) $invoice->fresh()->paid_amount);
        $this->assertEquals(2000.00, (float) $invoice->fresh()->balance_due);
        $this->assertEquals('partial', $invoice->fresh()->status);
        $this->assertEquals(2000.00, (float) $case->fresh()->balance_qar);

        // Record final payment of 2000 QAR
        $invoice->update([
            'paid_amount' => 3000.00,
            'balance_due' => 0.00,
            'status' => 'paid',
        ]);

        $this->assertEquals('paid', $invoice->fresh()->status);
        $this->assertEquals(0.00, (float) $invoice->fresh()->balance_due);
    }

    public function test_client_portal_timeline_filters_only_client_visible_events()
    {
        $client = Client::first();
        $case = VisaCase::first() ?? VisaCase::create([
            'client_id' => $client->id,
            'country_id' => Country::first()->id,
            'service_id' => Service::first()->id,
        ]);

        TimelineEvent::create([
            'case_id' => $case->id,
            'event_type' => 'status_changed',
            'title' => 'Client Visible: Biometrics Completed',
            'is_client_visible' => true,
        ]);

        TimelineEvent::create([
            'case_id' => $case->id,
            'event_type' => 'internal_note',
            'title' => 'Confidential Internal Staff Assessment',
            'is_client_visible' => false,
        ]);

        $visibleEvents = TimelineEvent::where('case_id', $case->id)
            ->where('is_client_visible', true)
            ->get();

        $this->assertTrue($visibleEvents->contains('title', 'Client Visible: Biometrics Completed'));
        $this->assertFalse($visibleEvents->contains('title', 'Confidential Internal Staff Assessment'));
    }
}
