<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadSource;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeadService
{
    /**
     * Ingest lead from any source (Website, WhatsApp, Meta Ads, Walk-in)
     * with duplicate detection and Round-Robin sales assignment.
     */
    public function ingestLead(array $data): Lead
    {
        return DB::transaction(function () use ($data) {
            $phone = $data['phone'] ?? $data['whatsapp'] ?? null;
            $email = $data['email'] ?? null;
            $passportNo = $data['passport_no'] ?? null;
            $qidNo = $data['qid_no'] ?? null;

            // 1. Duplicate Detection Check
            $duplicateQuery = Lead::query();
            if ($phone) {
                $duplicateQuery->where(function ($q) use ($phone) {
                    $q->where('phone', $phone)->orWhere('whatsapp', $phone);
                });
            }
            if ($email) {
                $duplicateQuery->orWhere('email', $email);
            }
            if ($passportNo) {
                $duplicateQuery->orWhere('passport_no', $passportNo);
            }
            if ($qidNo) {
                $duplicateQuery->orWhere('qid_no', $qidNo);
            }

            $existingLead = $duplicateQuery->latest()->first();

            $isDuplicate = false;
            $duplicateOfId = null;

            if ($existingLead) {
                $isDuplicate = true;
                $duplicateOfId = $existingLead->id;
            }

            // 2. Round-Robin Sales Agent Assignment
            $assignedTo = $data['assigned_to'] ?? null;
            if (! $assignedTo && ! $isDuplicate) {
                $salesAgents = User::role('sales_agent')
                    ->where('is_active', true)
                    ->get();

                if ($salesAgents->isNotEmpty()) {
                    // Find agent with the lowest count of open leads
                    $agentWithLeastLeads = $salesAgents->sortBy(function ($agent) {
                        return Lead::where('assigned_to', $agent->id)
                            ->whereIn('status', ['new', 'contacted', 'qualified', 'consultation'])
                            ->count();
                    })->first();

                    $assignedTo = $agentWithLeastLeads?->id;
                }
            }

            // Fallback to super_admin or unassigned if no sales agent available
            if (! $assignedTo) {
                $superAdmin = User::role('super_admin')->first();
                $assignedTo = $superAdmin?->id;
            }

            // 3. Resolve Lead Source
            $sourceId = $data['source_id'] ?? null;
            if (! $sourceId && ! empty($data['source_code'])) {
                $source = LeadSource::where('code', $data['source_code'])->first();
                $sourceId = $source?->id;
            }

            // 4. Create Lead
            $lead = Lead::create([
                'name' => $data['name'] ?? 'Inbound Prospect',
                'phone' => $data['phone'] ?? null,
                'whatsapp' => $data['whatsapp'] ?? $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'qatar_residence_status' => $data['qatar_residence_status'] ?? 'resident',
                'destination_country' => $data['destination_country'] ?? null,
                'visa_or_service_type' => $data['visa_or_service_type'] ?? null,
                'planned_travel_date' => $data['planned_travel_date'] ?? null,
                'source_id' => $sourceId,
                'campaign' => $data['campaign'] ?? null,
                'assigned_to' => $assignedTo,
                'status' => $isDuplicate ? 'duplicate' : 'new',
                'lead_score' => $isDuplicate ? 20 : ($data['lead_score'] ?? 50),
                'passport_no' => $passportNo,
                'qid_no' => $qidNo,
                'is_duplicate' => $isDuplicate,
                'duplicate_of_id' => $duplicateOfId,
                'last_contacted_at' => null,
            ]);

            // 5. Record Creation Activity
            LeadActivity::create([
                'lead_id' => $lead->id,
                'actor_id' => $assignedTo,
                'activity_type' => $isDuplicate ? 'duplicate_flag' : 'inbound_created',
                'title' => $isDuplicate ? 'Flagged as Duplicate Inquiry' : 'New Inbound Lead Created',
                'details' => $isDuplicate
                    ? "Inquiry matches previous lead record #{$existingLead->lead_number}" . (!empty($data['notes']) ? " | Notes: " . $data['notes'] : '')
                    : "Lead received from source: " . ($data['source_code'] ?? 'Direct') . (!empty($data['notes']) ? " | Notes: " . $data['notes'] : ''),
            ]);

            // 6. Trigger Automation Engine Workflows
            try {
                app(\App\Services\WorkflowExecutionService::class)->dispatch('lead.created', $lead, [
                    'source' => $data['source_code'] ?? 'website',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Workflow dispatch failed on lead creation: ' . $e->getMessage());
            }

            return $lead;
        });
    }

    /**
     * Update lead pipeline stage and fire automation triggers.
     */
    public function updateLeadStage(Lead $lead, string $newStage, ?string $actorId = null): Lead
    {
        $previousStage = $lead->status;
        if ($previousStage === $newStage) {
            return $lead;
        }

        $lead->update([
            'status' => $newStage,
            'last_contacted_at' => now(),
        ]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'actor_id' => $actorId,
            'activity_type' => 'status_change',
            'title' => 'Pipeline Stage Updated',
            'details' => "Lead moved from [" . ucfirst($previousStage) . "] to [" . ucfirst($newStage) . "] stage.",
        ]);

        try {
            app(\App\Services\WorkflowExecutionService::class)->dispatch('lead.stage_changed', $lead, [
                'previous_stage' => $previousStage,
                'new_stage' => $newStage,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Workflow dispatch failed on lead stage change: ' . $e->getMessage());
        }

        return $lead;
    }
}
