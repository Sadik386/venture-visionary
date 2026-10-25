<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'case_id',
        'lead_id',
        'assigned_to_id',
        'title',
        'appointment_type',
        'location_name',
        'meeting_link',
        'client_name',
        'client_phone',
        'client_email',
        'appointment_time',
        'status',
        'notes',
    ];

    protected $casts = [
        'appointment_time' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::created(function (Appointment $appointment) {
            try {
                app(\App\Services\WorkflowExecutionService::class)->dispatch('appointment.scheduled', $appointment);
            } catch (\Throwable $e) {
                // ignore
            }
        });

        static::updated(function (Appointment $appointment) {
            if ($appointment->wasChanged('status')) {
                $event = match ($appointment->status) {
                    'attended' => 'appointment.completed',
                    'cancelled' => 'appointment.cancelled',
                    default => 'appointment.updated',
                };
                try {
                    app(\App\Services\WorkflowExecutionService::class)->dispatch($event, $appointment, [
                        'previous_status' => $appointment->getOriginal('status'),
                        'new_status' => $appointment->status,
                    ]);
                } catch (\Throwable $e) {
                    // ignore
                }
            }
        });
    }

    public function visaCase()
    {
        return $this->belongsTo(VisaCase::class, 'case_id');
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }
}
