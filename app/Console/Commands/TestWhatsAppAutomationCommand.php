<?php

namespace App\Console\Commands;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Services\AiConsultantService;
use App\Services\LeadService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;

class TestWhatsAppAutomationCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'crm:test-whatsapp-automation 
                            {--phone=* : Specific phone numbers to test} 
                            {--lang=bilingual : Language for AI generation (bilingual, en, bn)}
                            {--dry-run : Only generate and display AI messages without calling external gateway}';

    /**
     * The console command description.
     */
    protected $description = 'Test CRM WhatsApp AI Automation and lead messaging pipeline for specified phone numbers';

    /**
     * Execute the console command.
     */
    public function handle(LeadService $leadService, WhatsAppService $whatsAppService, AiConsultantService $aiService): int
    {
        $this->info('================================================================');
        $this->info('   Venture Visionary CRM — WhatsApp AI Automation Test Suite    ');
        $this->info('================================================================');

        $customPhones = $this->option('phone');
        $lang = $this->option('lang') ?: 'bilingual';
        $dryRun = (bool) $this->option('dry-run');

        // Default test targets provided by the user
        $testProfiles = [
            '01628398056' => [
                'name' => 'Md Sadik Rahman',
                'phone' => '01628398056',
                'whatsapp' => '01628398056',
                'email' => 'mdsadikrahman386@gmail.com',
                'nationality' => 'Bangladeshi',
                'destination_country' => 'United Kingdom',
                'visa_or_service_type' => 'Tourist Visa',
                'notes' => 'Looking for UK Standard Visitor Visa assistance and appointment booking.',
            ],
            '01892048225' => [
                'name' => 'Engr. Tariqul Islam',
                'phone' => '01892048225',
                'whatsapp' => '01892048225',
                'email' => 'tariqul.invest@example.com',
                'nationality' => 'Bangladeshi',
                'destination_country' => 'State of Qatar',
                'visa_or_service_type' => 'Qatar LLC Business Setup',
                'notes' => 'Inquiry regarding 100% foreign ownership LLC registration in Doha.',
            ],
            '01866464603' => [
                'name' => 'Tanvir Ahmed',
                'phone' => '01866464603',
                'whatsapp' => '01866464603',
                'email' => 'tanvir.schengen@example.com',
                'nationality' => 'Bangladeshi',
                'destination_country' => 'Germany (Schengen)',
                'visa_or_service_type' => 'Business & Tourist Visa',
                'notes' => 'Attending trade expo in Munich; needs urgent slot monitoring.',
            ],
        ];

        // Filter if specific phone numbers requested
        if (! empty($customPhones)) {
            $filtered = [];
            foreach ($customPhones as $cp) {
                $cleaned = preg_replace('/[^0-9]/', '', $cp);
                if (isset($testProfiles[$cleaned])) {
                    $filtered[$cleaned] = $testProfiles[$cleaned];
                } else {
                    $filtered[$cleaned] = [
                        'name' => 'Test Client (' . substr($cleaned, -4) . ')',
                        'phone' => $cleaned,
                        'whatsapp' => $cleaned,
                        'destination_country' => 'United Kingdom',
                        'visa_or_service_type' => 'Consultation Inquiry',
                        'notes' => 'Custom CLI test lead',
                    ];
                }
            }
            $testProfiles = $filtered;
        }

        $results = [];

        foreach ($testProfiles as $phone => $profile) {
            $this->newLine();
            $this->line("Processing target: <fg=yellow>{$profile['name']}</> (<fg=cyan>{$phone}</>)...");

            // 1. Ingest Lead into CRM
            $lead = $leadService->ingestLead([
                'name' => $profile['name'],
                'phone' => $profile['phone'],
                'whatsapp' => $profile['whatsapp'],
                'email' => $profile['email'] ?? null,
                'nationality' => $profile['nationality'] ?? 'Bangladeshi',
                'destination_country' => $profile['destination_country'],
                'visa_or_service_type' => $profile['visa_or_service_type'],
                'source_code' => 'whatsapp',
                'campaign' => 'WhatsApp AI Automation Test',
                'notes' => $profile['notes'] ?? '',
            ]);

            $this->line(" -> Lead Ingested: <fg=green>{$lead->lead_number}</> (Status: {$lead->status})");

            // 2. Generate AI Consultation Message
            $aiMessage = $aiService->generateWelcomeMessage($lead, $lang);

            // 3. Dispatch or Dry-Run
            $dispatchStatus = 'Dry-Run';
            $gatewayResponse = null;
            $waMeUrl = $whatsAppService->getDirectChatUrl($phone, $aiMessage);

            if (! $dryRun) {
                $gatewayResponse = $whatsAppService->sendMessage($phone, $aiMessage, $profile['name']);
                if (($gatewayResponse['status'] ?? '') === 'success') {
                    $dispatchStatus = '<fg=green>Sent via LeminAI</>';
                } else {
                    $err = $gatewayResponse['error'] ?? 'failed';
                    $dispatchStatus = "<fg=red>Failed ({$err})</>";
                }

                // Log Lead Activity
                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'activity_type' => 'whatsapp',
                    'title' => 'Automated WhatsApp AI Message Dispatch',
                    'details' => $aiMessage,
                    'metadata' => [
                        'automated' => true,
                        'test_suite' => true,
                        'gateway_response' => $gatewayResponse,
                        'wa_me_url' => $waMeUrl,
                    ],
                ]);
            }

            $results[] = [
                'Lead #' => $lead->lead_number,
                'Name' => $profile['name'],
                'Phone' => $phone,
                'Destination' => $profile['destination_country'],
                'Status' => $dispatchStatus,
                'Direct 1-Click Link' => substr($waMeUrl, 0, 45) . '...',
            ];

            // Print AI Message Preview
            $this->comment(" -> AI Message Generated:");
            $this->line(str_repeat('-', 60));
            $this->line($aiMessage);
            $this->line(str_repeat('-', 60));
            if ($gatewayResponse) {
                $this->line(" -> Gateway Result: " . json_encode($gatewayResponse, JSON_UNESCAPED_SLASHES));
            }
            $this->line(" -> Direct Click-to-Chat Link: <href={$waMeUrl}>{$waMeUrl}</>");
        }

        $this->newLine();
        $this->info('Summary of WhatsApp AI Automation Run:');
        $this->table(['Lead #', 'Name', 'Phone', 'Destination', 'Status', 'Direct 1-Click Link'], $results);

        $this->newLine();
        $this->info("TIP: To message any contact directly using WhatsApp with the AI-generated consultation message, open the Click-to-Chat link above!");

        return Command::SUCCESS;
    }
}
