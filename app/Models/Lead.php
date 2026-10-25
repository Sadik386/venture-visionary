<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'lead_number',
        'name',
        'phone',
        'whatsapp',
        'email',
        'nationality',
        'qatar_residence_status',
        'destination_country',
        'visa_or_service_type',
        'planned_travel_date',
        'source_id',
        'campaign',
        'assigned_to',
        'status',
        'lead_score',
        'passport_no',
        'qid_no',
        'is_duplicate',
        'duplicate_of_id',
        'last_contacted_at',
    ];

    protected $casts = [
        'planned_travel_date' => 'date',
        'last_contacted_at' => 'datetime',
        'is_duplicate' => 'boolean',
        'lead_score' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Lead $lead) {
            if (empty($lead->lead_number)) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $lead->lead_number = 'LD-' . now()->format('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function source()
    {
        return $this->belongsTo(LeadSource::class, 'source_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function activities()
    {
        return $this->hasMany(LeadActivity::class);
    }
}
