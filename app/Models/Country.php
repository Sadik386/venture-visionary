<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'currency_code',
        'embassy_in_qatar_address',
        'official_vac_name',
        'official_portal_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function visaRequirements()
    {
        return $this->hasMany(VisaRequirement::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
