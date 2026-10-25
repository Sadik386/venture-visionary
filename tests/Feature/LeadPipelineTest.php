<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Client;
use Tests\TestCase;

class LeadPipelineTest extends TestCase
{
    public function test_can_ingest_website_lead_via_api(): void
    {
        $uniquePhone = '+974' . rand(50000000, 59999999);
        $response = $this->postJson('/api/v1/leads', [
            'name' => 'Dr. Jamal Al-Kuwari',
            'phone' => $uniquePhone,
            'whatsapp' => $uniquePhone,
            'email' => 'jamal.' . rand(100, 999) . '@example.qa',
            'destination_country' => 'United Kingdom',
            'visa_or_service_type' => 'Business Visa',
            'source_code' => 'website',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status' => 'success',
            'is_duplicate' => false,
        ]);

        $this->assertDatabaseHas('leads', [
            'phone' => $uniquePhone,
            'is_duplicate' => false,
        ]);
    }

    public function test_duplicate_lead_is_flagged_automatically(): void
    {
        $repeatPhone = '+97477112233';

        // First submission
        $this->postJson('/api/v1/leads', [
            'name' => 'Repeat Prospect',
            'phone' => $repeatPhone,
            'email' => 'repeat@example.qa',
            'destination_country' => 'France',
        ])->assertStatus(201);

        // Second submission with same phone
        $response = $this->postJson('/api/v1/leads', [
            'name' => 'Repeat Prospect 2',
            'phone' => $repeatPhone,
            'email' => 'repeat2@example.qa',
            'destination_country' => 'France',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'is_duplicate' => true,
        ]);
    }

    public function test_whatsapp_webhook_creates_lead(): void
    {
        $randomNum = rand(60000000, 69999999);
        $response = $this->postJson('/api/v1/webhooks/whatsapp', [
            'data' => [
                'key' => [
                    'remoteJid' => "974{$randomNum}@s.whatsapp.net",
                ],
                'pushName' => 'Sarah Al-Attiyah',
                'message' => [
                    'conversation' => 'Inquiry regarding UK visa requirements.',
                ],
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'message_received' => true,
        ]);

        $this->assertDatabaseHas('leads', [
            'phone' => "+974{$randomNum}",
        ]);
    }

    public function test_lead_number_format_is_strictly_ld_year_sequence(): void
    {
        $lead = Lead::create([
            'name' => 'Sequence Test User',
            'phone' => '+974' . rand(30000000, 39999999),
        ]);

        $this->assertMatchesRegularExpression('/^LD-\d{4}-\d{4}$/', $lead->lead_number);
    }

    public function test_lead_converts_to_master_client_profile(): void
    {
        $uniquePhone = '+974' . rand(40000000, 49999999);
        $lead = Lead::create([
            'name' => 'Hamad Al-Kuwari',
            'phone' => $uniquePhone,
            'whatsapp' => $uniquePhone,
            'email' => 'hamad.' . rand(100, 999) . '@example.qa',
            'nationality' => 'Qatar',
            'qatar_residence_status' => 'citizen',
            'qid_no' => '29063409999',
            'passport_no' => 'QA-999999',
        ]);

        $client = Client::create([
            'full_name' => $lead->name,
            'phone' => $lead->phone,
            'whatsapp' => $lead->whatsapp,
            'email' => $lead->email,
            'nationality' => $lead->nationality,
            'qid_number' => $lead->qid_no,
            'passport_number' => $lead->passport_no,
            'qatar_residence_status' => $lead->qatar_residence_status,
        ]);

        $lead->update(['status' => 'converted']);

        $this->assertMatchesRegularExpression('/^CL-\d{4}-\d{4}$/', $client->client_code);
        $this->assertEquals('converted', $lead->fresh()->status);
        $this->assertDatabaseHas('clients', ['id' => $client->id]);
    }

    public function test_lead_generation_page_renders_successfully(): void
    {
        $response = $this->get('/lead-generation');
        $response->assertStatus(200);
        $response->assertSee('Client Consultation Inquiry');
        $response->assertSee('Venture Visionary');

        // Test aliases
        $this->get('/inquiry')->assertRedirect('/lead-generation');
        $this->get('/leads/create')->assertRedirect('/lead-generation');
    }

    public function test_can_fetch_destinations_api(): void
    {
        $response = $this->getJson('/api/v1/destinations');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'count',
            'data' => [
                '*' => ['id', 'name', 'iso2', 'iso3', 'services']
            ]
        ]);
    }

    public function test_can_lookup_lead_status_by_lead_number_and_phone(): void
    {
        $uniquePhone = '+974' . rand(50000000, 59999999);
        $lead = Lead::create([
            'name' => 'Tracking Test User',
            'phone' => $uniquePhone,
            'whatsapp' => $uniquePhone,
            'destination_country' => 'United Kingdom',
            'visa_or_service_type' => 'Tourist Visa',
            'status' => 'new',
        ]);

        // Lookup by lead_number
        $resNum = $this->getJson('/api/v1/leads/lookup?query=' . $lead->lead_number);
        $resNum->assertStatus(200);
        $resNum->assertJson([
            'status' => 'success',
            'lead' => [
                'lead_number' => $lead->lead_number,
                'name' => 'Tracking Test User',
                'destination_country' => 'United Kingdom',
            ]
        ]);

        // Lookup by phone
        $resPhone = $this->getJson('/api/v1/leads/lookup?query=' . urlencode($uniquePhone));
        $resPhone->assertStatus(200);
        $resPhone->assertJson([
            'status' => 'success',
            'lead' => [
                'lead_number' => $lead->lead_number,
            ]
        ]);
    }

    public function test_can_fetch_leads_list_feed(): void
    {
        $response = $this->getJson('/api/v1/leads?limit=5');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'count',
            'leads' => [
                '*' => ['id', 'lead_number', 'name', 'phone_masked', 'status']
            ]
        ]);
    }
}
