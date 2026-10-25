<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Country;
use App\Models\Service;
use App\Models\Client;
use App\Models\VisaCase;
use App\Models\Partner;
use App\Models\PartnerUser;
use App\Models\PartnerWallet;
use App\Models\Lead;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Staff Departments
        $departments = [
            ['id' => Str::uuid(), 'name' => 'Executive Management', 'code' => 'MGMT'],
            ['id' => Str::uuid(), 'name' => 'Sales & Business Development', 'code' => 'SALES'],
            ['id' => Str::uuid(), 'name' => 'Visa Operations & QC', 'code' => 'VISA_OPS'],
            ['id' => Str::uuid(), 'name' => 'Visa Research & Governance', 'code' => 'VISA_RES'],
            ['id' => Str::uuid(), 'name' => 'Finance & Accounts', 'code' => 'FIN'],
            ['id' => Str::uuid(), 'name' => 'B2B Partner Relations', 'code' => 'B2B'],
            ['id' => Str::uuid(), 'name' => 'Customer Care & Support', 'code' => 'SUPPORT'],
        ];
        DB::table('staff_departments')->insertOrIgnore($departments);

        $mgmtDept = DB::table('staff_departments')->where('code', 'MGMT')->first();
        $salesDept = DB::table('staff_departments')->where('code', 'SALES')->first();
        $opsDept = DB::table('staff_departments')->where('code', 'VISA_OPS')->first();
        $finDept = DB::table('staff_departments')->where('code', 'FIN')->first();
        $resDept = DB::table('staff_departments')->where('code', 'VISA_RES')->first();

        // 2. Seed 12 Strict Roles (Section 20 & 26)
        $roles = [
            'super_admin',
            'general_manager',
            'operations_manager',
            'sales_manager',
            'sales_agent',
            'visa_officer',
            'qc_officer',
            'accounts_manager',
            'b2b_partner_admin',
            'b2b_agent',
            'client',
            'research_officer',
        ];
        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        }

        // 3. Seed Staff Users
        $staffUsers = [
            [
                'name' => 'Executive Super Admin',
                'email' => 'ceo@venturevisionary.qa',
                'department_id' => $mgmtDept?->id,
                'designation' => 'Chief Executive Officer',
                'role' => 'super_admin',
            ],
            [
                'name' => 'Tariq Al-Mansoor',
                'email' => 'gm@venturevisionary.qa',
                'department_id' => $mgmtDept?->id,
                'designation' => 'General Manager',
                'role' => 'general_manager',
            ],
            [
                'name' => 'Rashid Al-Kuwari',
                'email' => 'ops@venturevisionary.qa',
                'department_id' => $opsDept?->id,
                'designation' => 'Operations Director',
                'role' => 'operations_manager',
            ],
            [
                'name' => 'Sarah Jenkins',
                'email' => 'sales@venturevisionary.qa',
                'department_id' => $salesDept?->id,
                'designation' => 'Senior Visa Consultant',
                'role' => 'sales_agent',
            ],
            [
                'name' => 'Mohammed Hassan',
                'email' => 'visa@venturevisionary.qa',
                'department_id' => $opsDept?->id,
                'designation' => 'Visa Processing Officer',
                'role' => 'visa_officer',
            ],
            [
                'name' => 'Elena Rostova',
                'email' => 'qc@venturevisionary.qa',
                'department_id' => $opsDept?->id,
                'designation' => 'Quality Control Specialist',
                'role' => 'qc_officer',
            ],
            [
                'name' => 'Farhan Qureshi',
                'email' => 'accounts@venturevisionary.qa',
                'department_id' => $finDept?->id,
                'designation' => 'Finance Controller',
                'role' => 'accounts_manager',
            ],
            [
                'name' => 'Dr. Ziad Nabil',
                'email' => 'research@venturevisionary.qa',
                'department_id' => $resDept?->id,
                'designation' => 'Chief Immigration Intelligence Officer',
                'role' => 'research_officer',
            ],
        ];

        foreach ($staffUsers as $sUser) {
            $user = User::firstOrCreate(['email' => $sUser['email']], [
                'name' => $sUser['name'],
                'password' => Hash::make('Admin@VV2026!'),
                'department_id' => $sUser['department_id'],
                'designation' => $sUser['designation'],
                'role_code' => $sUser['role'],
                'is_active' => true,
            ]);
            $user->syncRoles([$sUser['role']]);
        }

        // 4. Seed Core Countries
        $countries = [
            ['id' => Str::uuid(), 'name' => 'Qatar', 'iso2' => 'QA', 'iso3' => 'QAT', 'currency_code' => 'QAR', 'official_vac_name' => null],
            ['id' => Str::uuid(), 'name' => 'United Kingdom', 'iso2' => 'GB', 'iso3' => 'GBR', 'currency_code' => 'GBP', 'official_vac_name' => 'VFS Global'],
            ['id' => Str::uuid(), 'name' => 'France (Schengen)', 'iso2' => 'FR', 'iso3' => 'FRA', 'currency_code' => 'EUR', 'official_vac_name' => 'TLScontact'],
            ['id' => Str::uuid(), 'name' => 'United States', 'iso2' => 'US', 'iso3' => 'USA', 'currency_code' => 'USD', 'official_vac_name' => 'US Embassy Doha'],
            ['id' => Str::uuid(), 'name' => 'Canada', 'iso2' => 'CA', 'iso3' => 'CAN', 'currency_code' => 'CAD', 'official_vac_name' => 'VFS Global'],
            ['id' => Str::uuid(), 'name' => 'Turkey', 'iso2' => 'TR', 'iso3' => 'TUR', 'currency_code' => 'USD', 'official_vac_name' => 'Kingston / VFS'],
            ['id' => Str::uuid(), 'name' => 'Saudi Arabia', 'iso2' => 'SA', 'iso3' => 'SAU', 'currency_code' => 'SAR', 'official_vac_name' => null],
            ['id' => Str::uuid(), 'name' => 'United Arab Emirates', 'iso2' => 'AE', 'iso3' => 'ARE', 'currency_code' => 'AED', 'official_vac_name' => null],
            ['id' => Str::uuid(), 'name' => 'Bangladesh', 'iso2' => 'BD', 'iso3' => 'BGD', 'currency_code' => 'BDT', 'official_vac_name' => null],
            ['id' => Str::uuid(), 'name' => 'India', 'iso2' => 'IN', 'iso3' => 'IND', 'currency_code' => 'INR', 'official_vac_name' => null],
        ];
        DB::table('countries')->insertOrIgnore($countries);

        // 5. Seed Service Categories
        $categories = [
            ['id' => Str::uuid(), 'name' => 'Visa Consultancy', 'code' => 'visa'],
            ['id' => Str::uuid(), 'name' => 'Company Formation & Commercial License', 'code' => 'company_formation'],
            ['id' => Str::uuid(), 'name' => 'Bank Account Assistance', 'code' => 'bank_assistance'],
            ['id' => Str::uuid(), 'name' => 'Document Attestation & Translation', 'code' => 'corporate_services'],
        ];
        DB::table('service_categories')->insertOrIgnore($categories);

        // 6. Seed Visa Types
        $visaTypes = [
            ['id' => Str::uuid(), 'name' => 'Tourist Visa', 'code' => 'tourist'],
            ['id' => Str::uuid(), 'name' => 'Business / Conference Visa', 'code' => 'business'],
            ['id' => Str::uuid(), 'name' => 'Invitation-Based Visa', 'code' => 'invitation'],
            ['id' => Str::uuid(), 'name' => 'Transit Visa', 'code' => 'transit'],
            ['id' => Str::uuid(), 'name' => 'Family Visit Visa', 'code' => 'family_visit'],
        ];
        DB::table('visa_types')->insertOrIgnore($visaTypes);

        // 7. Seed Document Types
        $documentTypes = [
            ['id' => Str::uuid(), 'name' => 'Passport Biodata Page', 'code' => 'passport_bio', 'has_expiry_date' => true],
            ['id' => Str::uuid(), 'name' => 'Qatar ID (QID)', 'code' => 'qid', 'has_expiry_date' => true],
            ['id' => Str::uuid(), 'name' => '6-Month Bank Statement', 'code' => 'bank_statement', 'has_expiry_date' => false],
            ['id' => Str::uuid(), 'name' => 'Company Salary Certificate / NOC', 'code' => 'salary_cert', 'has_expiry_date' => false],
            ['id' => Str::uuid(), 'name' => 'Recent Passport Photograph', 'code' => 'photo', 'has_expiry_date' => false],
            ['id' => Str::uuid(), 'name' => 'Confirmed Hotel Booking', 'code' => 'hotel_booking', 'has_expiry_date' => false],
            ['id' => Str::uuid(), 'name' => 'Flight Itinerary Reservation', 'code' => 'flight_reservation', 'has_expiry_date' => false],
            ['id' => Str::uuid(), 'name' => 'Travel Health Insurance', 'code' => 'travel_insurance', 'has_expiry_date' => true],
            ['id' => Str::uuid(), 'name' => 'Commercial Registration (CR) Copy', 'code' => 'cr_copy', 'has_expiry_date' => true],
        ];
        DB::table('document_types')->insertOrIgnore($documentTypes);

        // 8. Seed Lead Sources
        $sources = [
            ['id' => Str::uuid(), 'name' => 'Official Website Contact Form', 'code' => 'website'],
            ['id' => Str::uuid(), 'name' => 'Facebook / Meta Lead Ads', 'code' => 'meta_ads'],
            ['id' => Str::uuid(), 'name' => 'WhatsApp Direct Inbound', 'code' => 'whatsapp'],
            ['id' => Str::uuid(), 'name' => 'Google Ads Search Campaign', 'code' => 'google_ads'],
            ['id' => Str::uuid(), 'name' => 'B2B Partner Referral', 'code' => 'b2b_referral'],
            ['id' => Str::uuid(), 'name' => 'Office Walk-In', 'code' => 'walk_in'],
        ];
        DB::table('lead_sources')->insertOrIgnore($sources);

        // 9. Seed Core Services
        $visaCat = DB::table('service_categories')->where('code', 'visa')->first();
        $corpCat = DB::table('service_categories')->where('code', 'company_formation')->first();

        $ukCountry = Country::where('iso2', 'GB')->first();
        $frCountry = Country::where('iso2', 'FR')->first();
        $usCountry = Country::where('iso2', 'US')->first();
        $qaCountry = Country::where('iso2', 'QA')->first();

        $services = [
            [
                'code' => 'UK-TOURIST-STD',
                'category_id' => $visaCat->id,
                'country_id' => $ukCountry?->id,
                'name' => 'UK Standard Visitor Visa (6 Months)',
                'default_government_fee' => 550.00,
                'default_vac_fee' => 320.00,
                'default_consultancy_fee' => 950.00,
                'currency' => 'QAR',
                'standard_sla_days' => 15,
            ],
            [
                'code' => 'FR-SCHENGEN-TOURIST',
                'category_id' => $visaCat->id,
                'country_id' => $frCountry?->id,
                'name' => 'France / Schengen Short Stay Visa',
                'default_government_fee' => 360.00,
                'default_vac_fee' => 180.00,
                'default_consultancy_fee' => 850.00,
                'currency' => 'QAR',
                'standard_sla_days' => 12,
            ],
            [
                'code' => 'US-B1B2-VISIT',
                'category_id' => $visaCat->id,
                'country_id' => $usCountry?->id,
                'name' => 'United States B1/B2 Visitor Visa',
                'default_government_fee' => 675.00,
                'default_vac_fee' => 0.00,
                'default_consultancy_fee' => 1200.00,
                'currency' => 'QAR',
                'standard_sla_days' => 20,
            ],
            [
                'code' => 'QA-CORP-LLC',
                'category_id' => $corpCat->id,
                'country_id' => $qaCountry?->id,
                'name' => 'Qatar 100% Foreign Ownership LLC Formation',
                'default_government_fee' => 2500.00,
                'default_vac_fee' => 0.00,
                'default_consultancy_fee' => 7500.00,
                'currency' => 'QAR',
                'standard_sla_days' => 25,
            ],
        ];

        foreach ($services as $svc) {
            Service::firstOrCreate(['code' => $svc['code']], $svc);
        }

        // 10. Seed B2B Partner Agency & Partner User
        $superAdminUser = User::where('email', 'ceo@venturevisionary.qa')->first();
        $partner = Partner::firstOrCreate(['email' => 'info@dohaglobaltravel.qa'], [
            'company_name' => 'Doha Global Travels & Tourism WLL',
            'trade_license_number' => 'CR-99281-QA',
            'country' => 'Qatar',
            'city' => 'Doha',
            'contact_person' => 'Jassim Al-Sulaiti',
            'phone' => '+97444112233',
            'status' => 'active',
            'credit_limit_qar' => 75000.00,
            'payment_terms_days' => 30,
            'account_manager_id' => $superAdminUser?->id,
        ]);

        PartnerWallet::firstOrCreate(['partner_id' => $partner->id], [
            'balance' => 25000.00,
            'credit_limit' => 75000.00,
            'currency' => 'QAR',
        ]);

        $partnerAuthUser = User::firstOrCreate(['email' => 'partner@dohaglobaltravel.qa'], [
            'name' => 'Jassim Al-Sulaiti',
            'password' => Hash::make('Partner@2026!'),
            'role_code' => 'b2b_agent',
            'is_active' => true,
        ]);
        $partnerAuthUser->syncRoles(['b2b_agent']);

        PartnerUser::firstOrCreate(['email' => 'partner@dohaglobaltravel.qa'], [
            'partner_id' => $partner->id,
            'name' => 'Jassim Al-Sulaiti',
            'password' => Hash::make('Partner@2026!'),
            'phone' => '+97444112233',
            'role' => 'agent',
            'is_active' => true,
        ]);

        // 11. Seed Master Client Profile & Client Portal User
        $clientAuthUser = User::firstOrCreate(['email' => 'client@example.qa'], [
            'name' => 'Sheikh Khalid Al-Kuwari',
            'password' => Hash::make('Client@2026!'),
            'phone' => '+97455009988',
            'whatsapp' => '+97455009988',
            'role_code' => 'client',
            'is_active' => true,
        ]);
        $clientAuthUser->syncRoles(['client']);

        $client = Client::firstOrCreate(['email' => 'client@example.qa'], [
            'full_name' => 'Sheikh Khalid Al-Kuwari',
            'passport_name' => 'KHALID MOHAMMED AL-KUWARI',
            'phone' => '+97455009988',
            'whatsapp' => '+97455009988',
            'nationality' => 'Qatar',
            'passport_number' => 'QA-8829102',
            'passport_expiry_date' => now()->addYears(5),
            'qid_number' => '29063401928',
            'qid_expiry_date' => now()->addYears(2),
            'qatar_residence_status' => 'citizen',
            'qatar_address' => 'Villa 14, Al-Waab District, Doha, Qatar',
        ]);

        // 12. Seed Demonstration Visa Case
        $ukService = Service::where('code', 'UK-TOURIST-STD')->first();
        $visaOfficer = User::where('email', 'visa@venturevisionary.qa')->first();

        VisaCase::firstOrCreate(['client_id' => $client->id, 'service_id' => $ukService->id], [
            'country_id' => $ukCountry?->id,
            'assigned_officer_id' => $visaOfficer?->id,
            'status' => 'under_review',
            'priority' => 'vip',
            'intended_travel_date' => now()->addDays(45),
            'submission_deadline' => now()->addDays(20),
            'sla_due_at' => now()->addDays(15),
            'total_amount_qar' => 1820.00,
            'paid_amount_qar' => 1000.00,
            'balance_qar' => 820.00,
            'client_notes' => 'Your documents are currently under internal verification. Biometrics will take place at VFS Global Doha on Tuesday.',
            'internal_notes' => 'CONFIDENTIAL: Priority VIP applicant. Bank statement balance exceeds QAR 500k. No previous refusals.',
        ]);

        // 13. Seed Default GoHighLevel Automation Workflows
        $salesAgent = User::where('email', 'sales@venturevisionary.qa')->first() ?? $superAdmin;

        $welcomeWorkflow = \App\Models\Workflow::firstOrCreate(
            ['name' => 'Instant Lead Welcome & WhatsApp Acknowledgment'],
            [
                'trigger_event' => 'lead.created',
                'description' => 'Instantly sends WhatsApp acknowledgment to new prospect and alerts staff desk.',
                'is_active' => true,
            ]
        );
        \App\Models\WorkflowAction::firstOrCreate(
            ['workflow_id' => $welcomeWorkflow->id, 'action_type' => 'send_whatsapp'],
            [
                'order' => 1,
                'action_config' => [
                    'message' => 'Hello {name}, thank you for contacting Venture Visionary Doha. We have received your consultation inquiry regarding {destination} and our senior advisor will contact you shortly.',
                ],
                'is_active' => true,
            ]
        );
        \App\Models\WorkflowAction::firstOrCreate(
            ['workflow_id' => $welcomeWorkflow->id, 'action_type' => 'send_notification'],
            [
                'order' => 2,
                'action_config' => [
                    'title' => 'New Inbound Prospect',
                    'body' => 'New inquiry registered for {name} ({destination}).',
                ],
                'is_active' => true,
            ]
        );

        $hotLeadWorkflow = \App\Models\Workflow::firstOrCreate(
            ['name' => 'VIP Hot Lead Escalation & Immediate Task'],
            [
                'trigger_event' => 'lead.created',
                'description' => 'Automatically prioritizes leads with score >= 70 by creating an urgent 24h task.',
                'conditions' => ['lead_score_gte' => 70],
                'is_active' => true,
            ]
        );
        \App\Models\WorkflowAction::firstOrCreate(
            ['workflow_id' => $hotLeadWorkflow->id, 'action_type' => 'create_task'],
            [
                'order' => 1,
                'action_config' => [
                    'title' => 'URGENT: Call High-Intent Lead {name} ({destination})',
                    'description' => 'Lead score is 70+ indicating high booking velocity. Reach out within 2 hours.',
                    'priority' => 'urgent',
                    'due_days' => 1,
                    'assigned_to_id' => $salesAgent->id,
                ],
                'is_active' => true,
            ]
        );

        $qualifiedWorkflow = \App\Models\Workflow::firstOrCreate(
            ['name' => 'Qualified Lead Consultation Task Creation'],
            [
                'trigger_event' => 'lead.stage_changed',
                'description' => 'Triggers appointment scheduling task whenever a lead reaches Qualified stage.',
                'conditions' => ['status' => 'qualified'],
                'is_active' => true,
            ]
        );
        \App\Models\WorkflowAction::firstOrCreate(
            ['workflow_id' => $qualifiedWorkflow->id, 'action_type' => 'create_task'],
            [
                'order' => 1,
                'action_config' => [
                    'title' => 'Schedule Strategy Meeting for {name}',
                    'description' => 'Lead has been qualified. Prepare preliminary quotation and schedule 1-on-1 meeting.',
                    'priority' => 'high',
                    'due_days' => 2,
                    'assigned_to_id' => $salesAgent->id,
                ],
                'is_active' => true,
            ]
        );

        $reviewWorkflow = \App\Models\Workflow::firstOrCreate(
            ['name' => 'Automated 5-Star Review Request on Visa Approval'],
            [
                'trigger_event' => 'case.status_changed',
                'description' => 'Dispatches smart review invitation link to client immediately upon visa approval.',
                'conditions' => ['status' => 'approved'],
                'is_active' => true,
            ]
        );
        \App\Models\WorkflowAction::firstOrCreate(
            ['workflow_id' => $reviewWorkflow->id, 'action_type' => 'trigger_review_request'],
            [
                'order' => 1,
                'action_config' => [
                    'channel' => 'whatsapp',
                ],
                'is_active' => true,
            ]
        );

        // 14. Seed Sample Appointment & Review Request
        $sampleLead = \App\Models\Lead::first();
        if ($sampleLead) {
            \App\Models\Appointment::firstOrCreate(
                ['title' => 'UK Tourist Visa Strategy Consultation', 'lead_id' => $sampleLead->id],
                [
                    'assigned_to_id' => $salesAgent->id,
                    'appointment_type' => 'consultation',
                    'location_name' => 'Venture Visionary Advisory Office, Doha',
                    'client_name' => $sampleLead->name,
                    'client_phone' => $sampleLead->phone ?? $sampleLead->whatsapp,
                    'client_email' => $sampleLead->email,
                    'appointment_time' => now()->addDays(2)->setHour(11)->setMinute(0),
                    'status' => 'scheduled',
                    'notes' => 'Client traveling with family in December. Review financial documents.',
                ]
            );

            \App\Models\ReviewRequest::firstOrCreate(
                ['token' => 'sample-5star-token-2026'],
                [
                    'lead_id' => $sampleLead->id,
                    'recipient_name' => $sampleLead->name,
                    'recipient_phone' => $sampleLead->phone ?? $sampleLead->whatsapp,
                    'channel' => 'whatsapp',
                    'status' => 'completed',
                    'rating' => 5,
                    'feedback' => 'Exceptional professionalism by the Venture Visionary team. My UK visa was granted in record time with zero hassle!',
                    'public_review_redirected' => true,
                    'google_review_url' => 'https://search.google.com/local/writereview?placeid=ChIJQV_VentureVisionaryQatar',
                    'sent_at' => now()->subDays(3),
                    'completed_at' => now()->subDays(2),
                ]
            );
        }
    }
}
