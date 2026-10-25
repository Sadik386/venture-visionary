<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\Task;
use App\Models\User;
use App\Models\VisaCase;
use App\Models\Workflow;
use App\Models\WorkflowLog;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class WorkflowExecutionService
{
    /**
     * Dispatch an event through the Workflow Automation Engine.
     */
    public function dispatch(string $triggerEvent, Model $entity, array $context = []): int
    {
        $activeWorkflows = Workflow::where('trigger_event', $triggerEvent)
            ->where('is_active', true)
            ->with(['actions' => fn($q) => $q->where('is_active', true)->orderBy('order', 'asc')])
            ->get();

        $executedCount = 0;

        foreach ($activeWorkflows as $workflow) {
            if (! $this->matchesConditions($workflow, $entity, $context)) {
                WorkflowLog::create([
                    'workflow_id' => $workflow->id,
                    'trigger_event' => $triggerEvent,
                    'entity_type' => get_class($entity),
                    'entity_id' => (string) $entity->id,
                    'status' => 'skipped',
                    'details' => ['reason' => 'Conditions not met', 'context' => $context],
                    'created_at' => now(),
                ]);
                continue;
            }

            $actionLogs = [];

            foreach ($workflow->actions as $action) {
                try {
                    $result = $this->executeAction($action->action_type, $action->action_config ?? [], $entity, $context);
                    $actionLogs[] = [
                        'action' => $action->action_type,
                        'status' => 'success',
                        'result' => $result,
                    ];
                } catch (\Throwable $e) {
                    Log::error("Workflow [{$workflow->name}] Action [{$action->action_type}] failed: " . $e->getMessage());
                    $actionLogs[] = [
                        'action' => $action->action_type,
                        'status' => 'error',
                        'error' => $e->getMessage(),
                    ];
                }
            }

            // Update stats
            $workflow->increment('execution_count');
            $workflow->update(['last_triggered_at' => now()]);

            WorkflowLog::create([
                'workflow_id' => $workflow->id,
                'trigger_event' => $triggerEvent,
                'entity_type' => get_class($entity),
                'entity_id' => (string) $entity->id,
                'status' => 'success',
                'details' => ['actions' => $actionLogs, 'context' => $context],
                'created_at' => now(),
            ]);

            $executedCount++;
        }

        return $executedCount;
    }

    /**
     * Evaluate if the entity satisfies the workflow conditions.
     */
    protected function matchesConditions(Workflow $workflow, Model $entity, array $context): bool
    {
        $conditions = $workflow->conditions;
        if (empty($conditions) || ! is_array($conditions)) {
            return true;
        }

        foreach ($conditions as $key => $expectedValue) {
            // Check threshold for lead score
            if ($key === 'lead_score_gte') {
                $score = $entity->lead_score ?? 0;
                if ($score < (int) $expectedValue) {
                    return false;
                }
                continue;
            }

            // Check stage / status
            if ($key === 'status') {
                if (($entity->status ?? null) !== $expectedValue) {
                    return false;
                }
                continue;
            }

            // Check previous stage in context
            if ($key === 'previous_status') {
                if (($context['previous_stage'] ?? null) !== $expectedValue) {
                    return false;
                }
                continue;
            }

            // Check destination country
            if ($key === 'destination_country') {
                if (is_array($expectedValue)) {
                    if (! in_array($entity->destination_country ?? '', $expectedValue)) {
                        return false;
                    }
                } else {
                    if (($entity->destination_country ?? '') !== $expectedValue) {
                        return false;
                    }
                }
                continue;
            }

            // Check duplicate flag
            if ($key === 'is_duplicate') {
                if ((bool) ($entity->is_duplicate ?? false) !== (bool) $expectedValue) {
                    return false;
                }
                continue;
            }

            // Generic attribute fallback
            if (isset($entity->{$key}) && $entity->{$key} != $expectedValue) {
                return false;
            }
        }

        return true;
    }

    /**
     * Execute an individual workflow action.
     */
    protected function executeAction(string $actionType, array $config, Model $entity, array $context): mixed
    {
        $superAdmin = User::role('super_admin')->first() ?? User::first();
        $assignedUserId = $entity->assigned_to ?? $entity->assigned_to_id ?? $superAdmin?->id;

        switch ($actionType) {
            case 'create_task':
                $days = (int) ($config['due_days'] ?? 1);
                $title = str_replace(
                    ['{name}', '{lead_number}', '{destination}'],
                    [$entity->name ?? 'Prospect', $entity->lead_number ?? '', $entity->destination_country ?? 'General'],
                    $config['title'] ?? 'Automated Workflow Task'
                );

                return Task::create([
                    'case_id' => $entity instanceof VisaCase ? $entity->id : ($entity->case_id ?? null),
                    'assigned_to_id' => $config['assigned_to_id'] ?? $assignedUserId ?? $superAdmin?->id,
                    'created_by_id' => $superAdmin?->id ?? $assignedUserId,
                    'title' => $title,
                    'description' => $config['description'] ?? 'Generated by automated workflow engine.',
                    'priority' => $config['priority'] ?? 'high',
                    'status' => 'pending',
                    'due_at' => now()->addDays($days),
                ]);

            case 'send_notification':
                $title = str_replace('{name}', $entity->name ?? 'Lead', $config['title'] ?? 'Workflow Notification');
                $body = str_replace('{name}', $entity->name ?? 'Lead', $config['body'] ?? 'Action triggered by workflow.');

                $recipient = User::find($config['user_id'] ?? $assignedUserId) ?? $superAdmin;
                if ($recipient) {
                    Notification::make()
                        ->title($title)
                        ->body($body)
                        ->icon('heroicon-o-bolt')
                        ->iconColor('warning')
                        ->sendToDatabase($recipient);
                }
                return ['notified_user_id' => $recipient?->id];

            case 'send_whatsapp':
                $useAi = $config['use_ai'] ?? true;
                if ($useAi && $entity instanceof Lead) {
                    $aiService = app(\App\Services\AiConsultantService::class);
                    $language = $config['language'] ?? 'bilingual';
                    $message = $aiService->generateWelcomeMessage($entity, $language);
                } else {
                    $message = str_replace(
                        ['{name}', '{destination}'],
                        [$entity->name ?? 'Prospect', $entity->destination_country ?? 'Your requested destination'],
                        $config['message'] ?? 'Thank you for contacting Venture Visionary. A dedicated consultant has been assigned to your file.'
                    );
                }

                $targetPhone = $entity->whatsapp ?? $entity->phone ?? null;
                $apiResult = null;
                if ($targetPhone) {
                    $apiResult = app(\App\Services\WhatsAppService::class)->sendMessage(
                        $targetPhone,
                        $message,
                        $entity->name ?? 'Client'
                    );
                }

                if ($entity instanceof Lead) {
                    LeadActivity::create([
                        'lead_id' => $entity->id,
                        'activity_type' => 'whatsapp',
                        'title' => 'Automated WhatsApp AI Workflow Message',
                        'details' => $message,
                        'metadata' => [
                            'automated' => true,
                            'ai_generated' => $useAi,
                            'config' => $config,
                            'gateway_response' => $apiResult,
                            'wa_me_url' => $apiResult['wa_me_url'] ?? null,
                        ],
                    ]);
                }
                return ['message' => $message, 'dispatched' => true, 'gateway' => $apiResult];

            case 'assign_agent':
                if (! empty($config['user_id'])) {
                    if (isset($entity->assigned_to)) {
                        $entity->update(['assigned_to' => $config['user_id']]);
                    } elseif (isset($entity->assigned_to_id)) {
                        $entity->update(['assigned_to_id' => $config['user_id']]);
                    }
                    return ['assigned_to' => $config['user_id']];
                }
                return null;

            case 'trigger_review_request':
                $reputationService = app(ReputationService::class);
                $review = $reputationService->dispatchReviewRequest($entity, $config['channel'] ?? 'whatsapp');
                return ['review_request_id' => $review->id, 'token' => $review->token];

            case 'trigger_webhook':
                return [
                    'url' => $config['url'] ?? null,
                    'simulated' => true,
                    'timestamp' => now()->toIso8601String(),
                ];

            default:
                return ['skipped' => true, 'unknown_action' => $actionType];
        }
    }
}
