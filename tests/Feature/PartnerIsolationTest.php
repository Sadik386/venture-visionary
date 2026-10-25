<?php

namespace Tests\Feature;

use App\Filament\Partner\Resources\VisaCaseResource;
use App\Models\Client;
use App\Models\Partner;
use App\Models\PartnerUser;
use App\Models\Service;
use App\Models\User;
use App\Models\VisaCase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PartnerIsolationTest extends TestCase
{
    public function test_partner_portal_enforces_strict_tenant_isolation(): void
    {
        $service = Service::first();
        $client = Client::first();

        // Partner A
        $partnerA = Partner::firstOrCreate(['email' => 'partnerA@agency.qa'], [
            'company_name' => 'Agency Alpha Qatar',
            'contact_person' => 'Agent A',
            'phone' => '+97433001122',
            'country' => 'Qatar',
            'status' => 'active',
        ]);

        $userA = User::firstOrCreate(['email' => 'userA@agency.qa'], [
            'name' => 'Agent A',
            'password' => Hash::make('Secret@2026!'),
            'role_code' => 'b2b_agent',
            'is_active' => true,
        ]);
        $userA->syncRoles(['b2b_agent']);

        PartnerUser::firstOrCreate(['email' => 'userA@agency.qa'], [
            'partner_id' => $partnerA->id,
            'name' => 'Agent A',
            'password' => Hash::make('Secret@2026!'),
            'role' => 'agent',
        ]);

        // Partner B
        $partnerB = Partner::firstOrCreate(['email' => 'partnerB@agency.qa'], [
            'company_name' => 'Agency Beta Qatar',
            'contact_person' => 'Agent B',
            'phone' => '+97433002233',
            'country' => 'Qatar',
            'status' => 'active',
        ]);

        $userB = User::firstOrCreate(['email' => 'userB@agency.qa'], [
            'name' => 'Agent B',
            'password' => Hash::make('Secret@2026!'),
            'role_code' => 'b2b_agent',
            'is_active' => true,
        ]);
        $userB->syncRoles(['b2b_agent']);

        PartnerUser::firstOrCreate(['email' => 'userB@agency.qa'], [
            'partner_id' => $partnerB->id,
            'name' => 'Agent B',
            'password' => Hash::make('Secret@2026!'),
            'role' => 'agent',
        ]);

        // Create Case for Partner A
        $caseA = VisaCase::create([
            'partner_id' => $partnerA->id,
            'client_id' => $client->id,
            'service_id' => $service->id,
            'status' => 'submitted',
        ]);

        // Create Case for Partner B
        $caseB = VisaCase::create([
            'partner_id' => $partnerB->id,
            'client_id' => $client->id,
            'service_id' => $service->id,
            'status' => 'submitted',
        ]);

        // Authenticate as Partner A
        $this->actingAs($userA);

        $visibleToA = VisaCaseResource::getEloquentQuery()->pluck('id')->toArray();

        $this->assertContains($caseA->id, $visibleToA);
        $this->assertNotContains($caseB->id, $visibleToA);

        // Authenticate as Partner B
        $this->actingAs($userB);

        $visibleToB = VisaCaseResource::getEloquentQuery()->pluck('id')->toArray();

        $this->assertContains($caseB->id, $visibleToB);
        $this->assertNotContains($caseA->id, $visibleToB);
    }
}
