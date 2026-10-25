<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Country;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\ReviewRequest;
use App\Models\Task;
use App\Models\User;
use App\Models\VisaCase;
use App\Models\Workflow;
use App\Models\WorkflowLog;
use App\Services\LeadService;
use App\Services\ReputationService;
use App\Services\WorkflowExecutionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoHighLevelSalesAutomationTest extends TestCase
{
    protected User $admin;
    protected User $salesAgent;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('email', 'ceo@venturevisionary.qa')->first();
        $this->salesAgent = User::where('email', 'sales@venturevisionary.qa')->first() ?? $this->admin;
    }

    public function test_kanban_stage_transition_updates_lead_and_triggers_workflow(): void
    {
        $uniquePhone = '+974' . rand(80000000, 89999999);
        $lead = Lead::create([
            'name' => 'Sheikh Tamim Test',
            'phone' => $uniquePhone,
            'whatsapp' => $uniquePhone,
            'status' => 'new',
            'lead_score' => 60,
            'destination_country' => 'United Kingdom',
            'assigned_to' => $this->salesAgent->id,
        ]);

        $leadService = app(LeadService::class);
        $updatedLead = $leadService->updateLeadStage($lead, 'qualified', $this->salesAgent->id);

        $this->assertEquals('qualified', $updatedLead->status);

        // Verify LeadActivity logged
        $this->assertDatabaseHas('lead_activities', [
            'lead_id' => $lead->id,
            'activity_type' => 'status_change',
        ]);

        // Verify Workflow executed and created task
        $task = Task::where('title', 'like', '%Schedule Strategy Meeting for Sheikh Tamim Test%')->first();
        $this->assertNotNull($task, 'Task should have been created by qualified lead workflow');
        $this->assertEquals($this->salesAgent->id, $task->assigned_to_id);

        // Verify WorkflowLog
        $this->assertDatabaseHas('workflow_logs', [
            'trigger_event' => 'lead.stage_changed',
            'status' => 'success',
        ]);
    }

    public function test_workflow_engine_creates_task_and_whatsapp_activity_on_lead_ingest(): void
    {
        $leadService = app(LeadService::class);
        $uniquePhone = '+974' . rand(70000000, 79999999);
        $uniqueEmail = 'ahmad.' . rand(1000, 9999) . '@example.qa';

        $lead = $leadService->ingestLead([
            'name' => 'Ahmad High-Intent Prospect',
            'phone' => $uniquePhone,
            'whatsapp' => $uniquePhone,
            'email' => $uniqueEmail,
            'destination_country' => 'Canada',
            'visa_or_service_type' => 'Express Entry',
            'lead_score' => 85, // Hot lead >= 70
        ]);

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'status' => 'new',
            'lead_score' => 85,
        ]);

        // Verify Welcome WhatsApp was dispatched
        $this->assertDatabaseHas('lead_activities', [
            'lead_id' => $lead->id,
            'activity_type' => 'whatsapp',
        ]);

        // Verify VIP Hot Lead task was created
        $hotTask = Task::where('title', 'like', '%URGENT: Call High-Intent Lead Ahmad High-Intent Prospect%')->first();
        $this->assertNotNull($hotTask, 'VIP task should have been created for score >= 70');
        $this->assertEquals('urgent', $hotTask->priority);
    }

    public function test_public_appointment_booking_creates_lead_and_appointment(): void
    {
        $bookingTime = now()->addDays(3)->setHour(14)->setMinute(30)->second(0);
        $uniquePhone = '+974' . rand(30000000, 39999999);

        $response = $this->postJson('/api/v1/appointments/book', [
            'name' => 'Fatima Al-Ansari',
            'phone' => $uniquePhone,
            'email' => 'fatima.' . rand(100, 999) . '@example.qa',
            'destination_country' => 'United Kingdom',
            'service_type' => 'Tourist & Visitor Visa',
            'appointment_time' => $bookingTime->toIso8601String(),
            'notes' => 'Traveling for Eid vacation with family.',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'status',
                'message',
                'appointment_id',
                'lead_number',
                'assigned_consultant',
            ]);

        $this->assertDatabaseHas('appointments', [
            'client_name' => 'Fatima Al-Ansari',
            'status' => 'scheduled',
            'appointment_type' => 'consultation',
        ]);

        // Check that lead was created and moved to consultation stage
        $this->assertDatabaseHas('leads', [
            'name' => 'Fatima Al-Ansari',
            'status' => 'consultation',
        ]);
    }

    public function test_appointment_marked_attended_updates_status(): void
    {
        $appointment = Appointment::create([
            'title' => 'Interview Prep Session',
            'appointment_type' => 'interview',
            'location_name' => 'Embassy Desk',
            'client_name' => 'Tariq Hassan',
            'client_phone' => '+974' . rand(20000000, 29999999),
            'appointment_time' => now()->subHour(),
            'status' => 'scheduled',
        ]);

        $appointment->update(['status' => 'attended']);

        $this->assertEquals('attended', $appointment->fresh()->status);
    }

    public function test_reputation_review_request_and_smart_funnel(): void
    {
        $reputationService = app(ReputationService::class);
        $uniquePhone = '+974' . rand(40000000, 49999999);

        $lead = Lead::create([
            'name' => 'Happy Client Reviewer',
            'phone' => $uniquePhone,
            'whatsapp' => $uniquePhone,
            'status' => 'converted',
        ]);

        // 1. Dispatch Review Request
        $reviewRequest = $reputationService->dispatchReviewRequest($lead, 'whatsapp');
        $this->assertDatabaseHas('review_requests', [
            'id' => $reviewRequest->id,
            'token' => $reviewRequest->token,
            'status' => 'sent',
        ]);

        // 2. Submit 5-Star Positive Rating
        $postResponse = $this->post("/review/{$reviewRequest->token}", [
            'rating' => 5,
            'feedback' => 'Exceptional visa consulting experience in Doha!',
        ]);

        $postResponse->assertSessionHas('feedback_result');
        $result = session('feedback_result');
        $this->assertTrue($result['is_positive']);
        $this->assertNotNull($result['redirect_url']);

        $reviewRequest->refresh();
        $this->assertEquals(5, $reviewRequest->rating);
        $this->assertEquals('completed', $reviewRequest->status);
        $this->assertTrue($reviewRequest->public_review_redirected);

        // 3. Test Negative / Feedback Escalation (1-3 stars)
        $negToken = 'neg-token-' . rand(10000, 99999);
        $negativeRequest = ReviewRequest::create([
            'token' => $negToken,
            'recipient_name' => 'Dissatisfied Prospect',
            'recipient_phone' => '+974' . rand(10000000, 19999999),
            'status' => 'sent',
        ]);

        $negResponse = $this->post("/review/{$negativeRequest->token}", [
            'rating' => 2,
            'feedback' => 'Staff took 3 days to answer my query.',
        ]);

        $negResponse->assertSessionHas('feedback_result');
        $negResult = session('feedback_result');
        $this->assertFalse($negResult['is_positive']);
        $this->assertNull($negResult['redirect_url']);

        $negativeRequest->refresh();
        $this->assertEquals(2, $negativeRequest->rating);
        $this->assertFalse($negativeRequest->public_review_redirected);
    }

    public function test_all_filament_sales_and_automation_pages_render(): void
    {
        $this->actingAs($this->admin);

        // 1. Visual Kanban Pipeline
        $kanban = $this->get('/staff/lead-pipeline-board');
        $kanban->assertStatus(200);

        // 2. Appointment Calendar Resource
        $appointments = $this->get('/staff/appointments');
        $appointments->assertStatus(200);

        // 3. Workflow Engine Resource
        $workflows = $this->get('/staff/workflows');
        $workflows->assertStatus(200);

        // 4. Reputation & Review Requests Resource
        $reputation = $this->get('/staff/review-requests');
        $reputation->assertStatus(200);

        // 5. Public Booking Portal
        $publicBooking = $this->get('/book-appointment');
        $publicBooking->assertStatus(200);
    }
}
