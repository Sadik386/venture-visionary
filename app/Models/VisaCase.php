<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VisaCase extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'cases';

    protected $fillable = [
        'case_number',
        'client_id',
        'partner_id',
        'service_id',
        'country_id',
        'assigned_officer_id',
        'assigned_manager_id',
        'status',
        'priority',
        'intended_travel_date',
        'submission_deadline',
        'sla_due_at',
        'is_sla_breached',
        'payment_status',
        'total_amount_qar',
        'paid_amount_qar',
        'balance_qar',
        'checklist_snapshot',
        'pricing_snapshot',
        'workflow_snapshot',
        'internal_notes',
        'client_notes',
    ];

    protected $casts = [
        'intended_travel_date' => 'date',
        'submission_deadline' => 'date',
        'sla_due_at' => 'datetime',
        'is_sla_breached' => 'boolean',
        'total_amount_qar' => 'decimal:2',
        'paid_amount_qar' => 'decimal:2',
        'balance_qar' => 'decimal:2',
        // Rule 11: Immutable Historical Snapshots
        'checklist_snapshot' => 'array',
        'pricing_snapshot' => 'array',
        'workflow_snapshot' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (VisaCase $case) {
            if (empty($case->case_number)) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $case->case_number = 'CS-' . now()->format('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }

            // Rule 11: Immutable Historical Snapshot
            if (empty($case->pricing_snapshot) && $case->service_id) {
                $service = Service::find($case->service_id);
                if ($service) {
                    $case->pricing_snapshot = [
                        'service_name' => $service->name,
                        'service_code' => $service->code,
                        'default_government_fee' => (float) $service->default_government_fee,
                        'default_vac_fee' => (float) $service->default_vac_fee,
                        'default_consultancy_fee' => (float) $service->default_consultancy_fee,
                        'currency' => $service->currency,
                        'captured_at' => now()->toIso8601String(),
                    ];
                }
            }

            if (empty($case->checklist_snapshot)) {
                $case->checklist_snapshot = [
                    'mandatory_documents' => [
                        'passport_bio' => ['name' => 'Passport Biodata Page', 'required' => true, 'status' => 'requested'],
                        'qid' => ['name' => 'Qatar ID (QID)', 'required' => true, 'status' => 'requested'],
                        'bank_statement' => ['name' => '6-Month Bank Statement', 'required' => true, 'status' => 'requested'],
                        'salary_cert' => ['name' => 'Company Salary Certificate / NOC', 'required' => true, 'status' => 'requested'],
                        'photo' => ['name' => 'Recent Passport Photograph', 'required' => true, 'status' => 'requested'],
                    ],
                    'captured_at' => now()->toIso8601String(),
                ];
            }
        });

        static::saving(function (VisaCase $case) {
            $total = (float) $case->total_amount_qar;
            $paid = (float) $case->paid_amount_qar;
            $case->balance_qar = max(0, $total - $paid);
        });

        static::updated(function (VisaCase $case) {
            if ($case->wasChanged('status')) {
                try {
                    app(\App\Services\WorkflowExecutionService::class)->dispatch('case.status_changed', $case, [
                        'previous_status' => $case->getOriginal('status'),
                        'new_status' => $case->status,
                    ]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function assignedOfficer()
    {
        return $this->belongsTo(User::class, 'assigned_officer_id');
    }

    public function documents()
    {
        return $this->hasMany(CaseDocument::class, 'case_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'case_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'case_id');
    }

    public function timelineEvents()
    {
        return $this->hasMany(TimelineEvent::class, 'case_id')->orderBy('created_at', 'desc');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'case_id');
    }
}
