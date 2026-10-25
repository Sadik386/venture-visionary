<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimelineEvent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'case_id',
        'actor_id',
        'event_type',
        'title',
        'description',
        'is_client_visible',
        'metadata',
    ];

    protected $casts = [
        'is_client_visible' => 'boolean',
        'metadata'          => 'array',
    ];

    public function visaCase()
    {
        return $this->belongsTo(VisaCase::class, 'case_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
