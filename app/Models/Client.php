<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'client_code',
        'full_name',
        'passport_name',
        'date_of_birth',
        'gender',
        'nationality',
        'passport_number',
        'passport_expiry_date',
        'qid_number',
        'qid_expiry_date',
        'qatar_residence_status',
        'qatar_address',
        'phone',
        'whatsapp',
        'email',
        'travel_history',
        'business_profile',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry_date' => 'date',
        'qid_expiry_date' => 'date',
        'travel_history' => 'array',
        'business_profile' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Client $client) {
            if (empty($client->client_code)) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $client->client_code = 'CL-' . now()->format('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // Rule 4: Client != Case (1 Client has many Cases)
    public function cases()
    {
        return $this->hasMany(VisaCase::class);
    }

    public function masterDocuments()
    {
        return $this->hasMany(ClientDocument::class);
    }

    public function contacts()
    {
        return $this->hasMany(ClientContact::class);
    }
}
