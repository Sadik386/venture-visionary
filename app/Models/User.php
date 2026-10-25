<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable, HasUuids, HasRoles, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'whatsapp',
        'password',
        'department_id',
        'designation',
        'role_code',
        'is_active',
        'two_factor_enabled',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'two_factor_enabled' => 'boolean',
        ];
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function assignedLeads()
    {
        return $this->hasMany(Lead::class, 'assigned_to');
    }

    public function assignedCases()
    {
        return $this->hasMany(VisaCase::class, 'assigned_officer_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->hasRole('super_admin')) {
            return true;
        }

        if ($panel->getId() === 'staff') {
            return $this->hasAnyRole([
                'general_manager',
                'operations_manager',
                'sales_manager',
                'sales_agent',
                'visa_officer',
                'qc_officer',
                'accounts_manager',
                'research_officer',
            ]);
        }

        if ($panel->getId() === 'client') {
            return $this->hasRole('client');
        }

        if ($panel->getId() === 'partner') {
            return $this->hasAnyRole(['b2b_partner_admin', 'b2b_agent']);
        }

        return false;
    }
}
