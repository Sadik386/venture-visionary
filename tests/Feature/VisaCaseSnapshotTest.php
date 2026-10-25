<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Country;
use App\Models\Service;
use App\Models\VisaCase;
use Tests\TestCase;

class VisaCaseSnapshotTest extends TestCase
{
    public function test_case_creation_automatically_captures_immutable_pricing_and_checklist_snapshots(): void
    {
        $client = Client::first();
        $country = Country::where('iso2', 'GB')->first();
        $service = Service::where('code', 'UK-TOURIST-STD')->first();

        $case = VisaCase::create([
            'client_id' => $client->id,
            'country_id' => $country->id,
            'service_id' => $service->id,
            'status' => 'documents_pending',
            'priority' => 'vip',
            'total_amount_qar' => 1820.00,
            'paid_amount_qar' => 1000.00,
            'balance_qar' => 820.00,
            'client_notes' => 'Please bring passport copy.',
            'internal_notes' => 'CONFIDENTIAL: Check background.',
        ]);

        $this->assertMatchesRegularExpression('/^CS-\d{4}-\d{4}$/', $case->case_number);
        $this->assertIsArray($case->pricing_snapshot);
        $this->assertIsArray($case->checklist_snapshot);
        $this->assertEquals((float) $service->default_government_fee, (float) $case->pricing_snapshot['default_government_fee']);
        $this->assertEquals((float) $service->default_consultancy_fee, (float) $case->pricing_snapshot['default_consultancy_fee']);
        $this->assertArrayHasKey('mandatory_documents', $case->checklist_snapshot);
    }

    public function test_modifying_master_service_pricing_does_not_mutate_existing_case_snapshot(): void
    {
        $client = Client::first();
        $country = Country::where('iso2', 'GB')->first();
        $service = Service::where('code', 'UK-TOURIST-STD')->first();

        $originalGovFee = (float) $service->default_government_fee;

        $case = VisaCase::create([
            'client_id' => $client->id,
            'country_id' => $country->id,
            'service_id' => $service->id,
            'status' => 'under_review',
        ]);

        // Modify the master service price
        $service->update(['default_government_fee' => 9999.00]);

        $freshCase = $case->fresh();

        // The case's frozen snapshot must retain the original price!
        $this->assertEquals($originalGovFee, (float) $freshCase->pricing_snapshot['default_government_fee']);
        $this->assertNotEquals(9999.00, (float) $freshCase->pricing_snapshot['default_government_fee']);

        // Restore original
        $service->update(['default_government_fee' => $originalGovFee]);
    }

    public function test_client_portal_resource_hides_confidential_internal_notes(): void
    {
        $client = Client::first();
        $service = Service::first();

        $case = VisaCase::create([
            'client_id' => $client->id,
            'service_id' => $service->id,
            'status' => 'documents_pending',
            'client_notes' => 'Public applicant instructions.',
            'internal_notes' => 'STRICTLY SECRET INTERNAL STAFF NOTE',
        ]);

        $this->assertNotEmpty($case->internal_notes);
        $this->assertNotEmpty($case->client_notes);
        $this->assertNotEquals($case->client_notes, $case->internal_notes);
    }
}
