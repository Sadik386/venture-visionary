<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'partner_code',
        'company_name',
        'trade_license_number',
        'country',
        'city',
        'contact_person',
        'phone',
        'email',
        'status',
        'credit_limit_qar',
        'payment_terms_days',
        'agreement_expiry_date',
        'account_manager_id',
    ];

    protected $casts = [
        'credit_limit_qar' => 'decimal:2',
        'payment_terms_days' => 'integer',
        'agreement_expiry_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Partner $partner) {
            if (empty($partner->partner_code)) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $partner->partner_code = 'B2B-' . now()->format('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function users()
    {
        return $this->hasMany(PartnerUser::class);
    }

    public function netRates()
    {
        return $this->hasMany(PartnerPricing::class);
    }

    public function wallet()
    {
        return $this->hasOne(PartnerWallet::class);
    }

    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id');
    }
}
