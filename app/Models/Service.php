<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'category_id',
        'country_id',
        'visa_type_id',
        'name',
        'code',
        'description',
        'default_government_fee',
        'default_vac_fee',
        'default_consultancy_fee',
        'currency',
        'standard_sla_days',
        'is_active',
    ];

    protected $casts = [
        'default_government_fee' => 'decimal:2',
        'default_vac_fee' => 'decimal:2',
        'default_consultancy_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'standard_sla_days' => 'integer',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function rules()
    {
        return $this->hasMany(ServiceRule::class);
    }

    public function cases()
    {
        return $this->hasMany(VisaCase::class);
    }
}
