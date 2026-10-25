<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'trigger_event',
        'description',
        'conditions',
        'is_active',
        'execution_count',
        'last_triggered_at',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active' => 'boolean',
        'execution_count' => 'integer',
        'last_triggered_at' => 'datetime',
    ];

    public function actions()
    {
        return $this->hasMany(WorkflowAction::class)->orderBy('order', 'asc');
    }

    public function logs()
    {
        return $this->hasMany(WorkflowLog::class)->latest('created_at');
    }
}
