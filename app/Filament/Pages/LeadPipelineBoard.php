<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\Country;
use App\Models\Lead;
use App\Models\User;
use App\Services\LeadService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;

class LeadPipelineBoard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-view-columns';

    protected static ?string $navigationGroup = 'CRM & Sales';

    protected static ?string $navigationLabel = 'Visual Sales Pipeline';

    protected static ?string $title = 'Visual Lead Pipeline (Kanban)';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.lead-pipeline-board';

    public string $activeFilter = 'all';

    public string $search = '';

    public ?string $destinationFilter = null;

    public array $stages = [
        'new' => [
            'name' => 'New Inbound',
            'badge' => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-200',
            'bar' => 'bg-gray-400',
        ],
        'contacted' => [
            'name' => 'Contacted',
            'badge' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-200',
            'bar' => 'bg-blue-500',
        ],
        'qualified' => [
            'name' => 'Qualified',
            'badge' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/50 dark:text-amber-200',
            'bar' => 'bg-amber-500',
        ],
        'consultation' => [
            'name' => 'Consultation Scheduled',
            'badge' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-200',
            'bar' => 'bg-indigo-500',
        ],
        'quotation_sent' => [
            'name' => 'Quotation Sent',
            'badge' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/50 dark:text-purple-200',
            'bar' => 'bg-purple-500',
        ],
        'follow_up' => [
            'name' => 'Follow-up Required',
            'badge' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-200',
            'bar' => 'bg-orange-500',
        ],
        'converted' => [
            'name' => 'Converted to Client',
            'badge' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-200',
            'bar' => 'bg-emerald-500',
        ],
        'lost' => [
            'name' => 'Lost / Disqualified',
            'badge' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/50 dark:text-rose-200',
            'bar' => 'bg-rose-500',
        ],
    ];

    public function setFilter(string $filter): void
    {
        $this->activeFilter = $filter;
    }

    public function updateLeadStage(string $leadId, string $newStage): void
    {
        if (! array_key_exists($newStage, $this->stages)) {
            return;
        }

        $lead = Lead::findOrFail($leadId);
        $oldStage = $lead->status;

        if ($oldStage === $newStage) {
            return;
        }

        app(LeadService::class)->updateLeadStage($lead, $newStage, auth()->id());

        Notification::make()
            ->title('Lead Stage Updated')
            ->body("{$lead->name} (#{$lead->lead_number}) moved to {$this->stages[$newStage]['name']}.")
            ->success()
            ->send();
    }

    public function convertToClient(string $leadId): void
    {
        $lead = Lead::findOrFail($leadId);

        if ($lead->converted_client_id) {
            Notification::make()
                ->title('Already Converted')
                ->body("This lead is already converted to client {$lead->convertedClient?->client_code}.")
                ->warning()
                ->send();
            return;
        }

        // Generate client code CL-YYYY-XXXX
        $count = Client::whereYear('created_at', now()->year)->count() + 1;
        $clientCode = 'CL-' . now()->format('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        $client = Client::create([
            'client_code' => $clientCode,
            'full_name' => $lead->name,
            'phone' => $lead->phone ?? $lead->whatsapp ?? '+97400000000',
            'whatsapp' => $lead->whatsapp ?? $lead->phone,
            'email' => $lead->email,
            'nationality' => $lead->nationality ?? 'Unspecified',
            'qatar_residence_status' => $lead->qatar_residence_status ?? 'resident',
            'passport_number' => $lead->passport_no,
            'qid_number' => $lead->qid_no,
            'assigned_consultant_id' => $lead->assigned_to ?? auth()->id(),
        ]);

        $lead->update([
            'status' => 'converted',
            'converted_client_id' => $client->id,
        ]);

        app(LeadService::class)->updateLeadStage($lead, 'converted', auth()->id());

        Notification::make()
            ->title('Converted to Client 360')
            ->body("Created Client Profile {$client->client_code} for {$client->full_name}.")
            ->success()
            ->send();
    }

    public function getLeadsGroupedByStageProperty(): Collection
    {
        $query = Lead::with(['assignedTo:id,name', 'source:id,name'])
            ->where('is_duplicate', false);

        if (! empty($this->search)) {
            $s = trim($this->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('lead_number', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('destination_country', 'like', "%{$s}%");
            });
        }

        if (! empty($this->destinationFilter)) {
            $query->where('destination_country', $this->destinationFilter);
        }

        // Apply GoHighLevel Smart Lists
        switch ($this->activeFilter) {
            case 'hot':
                $query->where('lead_score', '>=', 70);
                break;
            case 'follow_up':
                $query->where(function ($q) {
                    $q->where('status', 'follow_up')
                      ->orWhere(function ($sub) {
                          $sub->whereNull('last_contacted_at')
                              ->where('created_at', '<=', now()->subDays(2))
                              ->whereNotIn('status', ['converted', 'lost']);
                      });
                });
                break;
            case 'unassigned':
                $query->whereNull('assigned_to');
                break;
            case 'high_intent':
                $query->whereIn('destination_country', [
                    'United Kingdom', 'Schengen Area', 'Canada', 'United States', 'Qatar Business Setup', 'Australia'
                ]);
                break;
        }

        $leads = $query->latest()->get();

        $grouped = collect();
        foreach (array_keys($this->stages) as $stageKey) {
            $grouped[$stageKey] = $leads->where('status', $stageKey)->values();
        }

        return $grouped;
    }

    public function getDestinationsProperty(): Collection
    {
        return Lead::whereNotNull('destination_country')
            ->select('destination_country')
            ->distinct()
            ->pluck('destination_country')
            ->filter();
    }
}
