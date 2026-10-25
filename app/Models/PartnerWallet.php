<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerWallet extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'partner_id',
        'balance',
        'credit_limit',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'credit_limit' => 'decimal:2',
    ];

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function transactions()
    {
        return $this->hasMany(PartnerTransaction::class);
    }
}
