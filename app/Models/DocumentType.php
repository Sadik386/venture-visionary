<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'code',
        'description',
        'has_expiry_date',
        'default_validity_months',
        'is_active',
    ];

    protected $casts = [
        'has_expiry_date' => 'boolean',
        'default_validity_months' => 'integer',
        'is_active' => 'boolean',
    ];
}
