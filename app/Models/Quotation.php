<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quotation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'quotation_number',
        'client_id',
        'partner_id',
        'case_id',
        'government_fee',
        'vac_fee',
        'consultancy_fee',
        'other_charges',
        'discount_amount',
        'tax_amount',
        'total_amount',
        'currency',
        'exchange_rate_to_qar',
        'valid_until',
        'status',
        'created_by_id',
    ];

    protected $casts = [
        'government_fee' => 'decimal:2',
        'vac_fee' => 'decimal:2',
        'consultancy_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'exchange_rate_to_qar' => 'decimal:4',
        'valid_until' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Quotation $quotation) {
            if (empty($quotation->quotation_number)) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $quotation->quotation_number = 'QT-' . now()->format('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }

            if (empty($quotation->created_by_id)) {
                $quotation->created_by_id = auth()->id() ?? User::value('id');
            }

            if (empty($quotation->total_amount)) {
                $quotation->total_amount = (float) $quotation->government_fee
                    + (float) $quotation->vac_fee
                    + (float) $quotation->consultancy_fee
                    + (float) $quotation->other_charges
                    - (float) $quotation->discount_amount;
            }
        });

        static::saving(function (Quotation $quotation) {
            if (empty($quotation->total_amount) || $quotation->isDirty(['government_fee', 'vac_fee', 'consultancy_fee', 'other_charges', 'discount_amount'])) {
                $quotation->total_amount = (float) $quotation->government_fee
                    + (float) $quotation->vac_fee
                    + (float) $quotation->consultancy_fee
                    + (float) $quotation->other_charges
                    - (float) $quotation->discount_amount;
            }
        });
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function visaCase()
    {
        return $this->belongsTo(VisaCase::class, 'case_id');
    }
}
