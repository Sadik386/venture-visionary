<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowAction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'workflow_id',
        'action_type',
        'action_config',
        'order',
        'is_active',
    ];

    protected $casts = [
        'action_config' => 'array',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
}
