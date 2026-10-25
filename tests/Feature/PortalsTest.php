<?php

namespace Tests\Feature;

use Tests\TestCase;

class PortalsTest extends TestCase
{
    public function test_root_route_returns_operational_status(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertJson([
            'system' => 'Venture Visionary CRM & Business Consultancy Platform',
            'version' => '2.0.0',
            'status' => 'operational',
        ]);
    }

    public function test_health_check_returns_ok(): void
    {
        $response = $this->get('/api/v1/health');
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'database' => 'connected',
        ]);
    }

    public function test_all_portals_login_pages_render_successfully(): void
    {
        $this->get('/staff/login')->assertStatus(200);
        $this->get('/portal/login')->assertStatus(200);
        $this->get('/partner/login')->assertStatus(200);
    }

    public function test_unauthenticated_portals_redirect_to_login(): void
    {
        $this->get('/staff')->assertRedirect('/staff/login');
        $this->get('/portal')->assertRedirect('/portal/login');
        $this->get('/partner')->assertRedirect('/partner/login');
    }

    public function test_openapi_documentation_route_returns_200(): void
    {
        $this->get('/docs/api')->assertStatus(200);
        $this->get('/docs/api.json')->assertStatus(200);
    }

    public function test_cross_portal_unauthorized_user_is_gracefully_redirected(): void
    {
        $client = \App\Models\User::where('email', 'client@example.qa')->first();
        if ($client) {
            $this->actingAs($client)->get('/staff')->assertRedirect('http://localhost:8090/portal');
        }

        $partnerUser = \App\Models\User::where('email', 'partner@dohaglobaltravel.qa')->first();
        if ($partnerUser) {
            $this->actingAs($partnerUser)->get('/staff')->assertRedirect('http://localhost:8090/partner');
        }

        $salesAgent = \App\Models\User::where('email', 'sales@venturevisionary.qa')->first();
        if ($salesAgent) {
            $this->actingAs($salesAgent)->get('/portal')->assertRedirect('http://localhost:8090/staff');
        }
    }
}
