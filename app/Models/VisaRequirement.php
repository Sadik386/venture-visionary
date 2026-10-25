<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisaRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'visa_type_id',
        'biometrics_required',
        'interview_required',
        'estimated_processing_days',
        'qatar_resident_eligibility',
        'nationality_specific_rules',
        'official_fee_qar',
        'vac_fee_qar',
        'verification_status',
        'last_verified_date',
        'next_review_date',
    ];

    protected $casts = [
        'biometrics_required' => 'boolean',
        'interview_required' => 'boolean',
        'estimated_processing_days' => 'integer',
        'qatar_resident_eligibility' => 'array',
        'nationality_specific_rules' => 'array',
        'official_fee_qar' => 'decimal:2',
        'vac_fee_qar' => 'decimal:2',
        'last_verified_date' => 'date',
        'next_review_date' => 'date',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function visaType()
    {
        return $this->belongsTo(VisaType::class);
    }
}
