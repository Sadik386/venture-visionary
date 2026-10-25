<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Workflow;
use App\Models\WorkflowAction;
use App\Services\AiConsultantService;
use App\Services\LeadService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppAiAutomationTest extends TestCase
{
    public function test_ai_consultant_generates_contextual_messages(): void
    {
        $service = app(AiConsultantService::class);

        // Test UK Tourist Visa
        $ukLead = [
            'name' => 'Md Sadik Rahman',
            'destination_country' => 'United Kingdom',
            'visa_or_service_type' => 'Tourist Visa',
            'lead_number' => 'LD-2026-TEST1',
        ];
        $ukMsg = $service->generateWelcomeMessage($ukLead, 'bilingual');
        $this->assertStringContainsString('Md Sadik Rahman', $ukMsg);
        $this->assertStringContainsString('United Kingdom — Tourist Visa', $ukMsg);
        $this->assertStringContainsString('UK Standard Visitor', $ukMsg);
        $this->assertStringContainsString('LD-2026-TEST1', $ukMsg);

        // Test Qatar Business Setup
        $qatarLead = [
            'name' => 'Engr. Tariqul Islam',
            'destination_country' => 'State of Qatar',
            'visa_or_service_type' => 'Qatar LLC Business Setup',
            'lead_number' => 'LD-2026-TEST2',
        ];
        $qatarMsg = $service->generateWelcomeMessage($qatarLead, 'bilingual');
        $this->assertStringContainsString('Engr. Tariqul Islam', $qatarMsg);
        $this->assertStringContainsString('100% foreign ownership', $qatarMsg);
        $this->assertStringContainsString('Commercial Registration (CR)', $qatarMsg);

        // Test Schengen Visa
        $schengenLead = [
            'name' => 'Tanvir Ahmed',
            'destination_country' => 'Germany (Schengen)',
            'visa_or_service_type' => 'Business & Tourist Visa',
            'lead_number' => 'LD-2026-TEST3',
        ];
        $schengenMsg = $service->generateWelcomeMessage($schengenLead, 'bilingual');
        $this->assertStringContainsString('Tanvir Ahmed', $schengenMsg);
        $this->assertStringContainsString('Schengen Type-C', $schengenMsg);
        $this->assertStringContainsString('TLScontact', $schengenMsg);
    }

    public function test_whatsapp_service_normalizes_bangladesh_numbers_and_generates_direct_url(): void
    {
        $wa = app(WhatsAppService::class);

        $normalized = $wa->normalizePhone('01628398056');
        $this->assertEquals('8801628398056', $normalized);

        $url = $wa->getDirectChatUrl('01628398056', 'Hello from Venture Visionary');
        $this->assertStringStartsWith('https://wa.me/8801628398056?text=', $url);
        $this->assertStringContainsString('Hello%20from%20Venture%20Visionary', $url);
    }

    public function test_artisan_command_executes_successfully_in_dry_run(): void
    {
        $this->artisan('crm:test-whatsapp-automation', [
            '--dry-run' => true,
        ])
        ->expectsOutputToContain('Venture Visionary CRM — WhatsApp AI Automation Test Suite')
        ->expectsOutputToContain('01628398056')
        ->expectsOutputToContain('01892048225')
        ->expectsOutputToContain('01866464603')
        ->assertExitCode(0);
    }
}
