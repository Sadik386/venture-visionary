<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'invoice_id',
        'case_id',
        'receipt_number',
        'amount',
        'currency',
        'exchange_rate_to_qar',
        'amount_in_qar',
        'payment_method',
        'transaction_reference',
        'payment_date',
        'received_by_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_in_qar' => 'decimal:2',
        'exchange_rate_to_qar' => 'decimal:4',
        'payment_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function visaCase()
    {
        return $this->belongsTo(VisaCase::class, 'case_id');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by_id');
    }
}
