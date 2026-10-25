<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'rule_name',
        'version',
        'eligibility_conditions',
        'document_checklist_rules',
        'is_active',
    ];

    protected $casts = [
        'version' => 'integer',
        'eligibility_conditions' => 'array',
        'document_checklist_rules' => 'array',
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
