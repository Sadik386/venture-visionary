<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'token',
        'lead_id',
        'case_id',
        'client_id',
        'recipient_name',
        'recipient_phone',
        'recipient_email',
        'channel',
        'status',
        'rating',
        'feedback',
        'public_review_redirected',
        'google_review_url',
        'sent_at',
        'completed_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'public_review_redirected' => 'boolean',
        'sent_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function visaCase()
    {
        return $this->belongsTo(VisaCase::class, 'case_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
