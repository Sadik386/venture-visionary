<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'invoice_number',
        'case_id',
        'client_id',
        'partner_id',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'paid_amount',
        'balance_due',
        'currency',
        'exchange_rate_to_qar',
        'issue_date',
        'due_date',
        'status',
        'created_by_id',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance_due' => 'decimal:2',
        'exchange_rate_to_qar' => 'decimal:4',
        'issue_date' => 'date',
        'due_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::creating(function (Invoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $invoice->invoice_number = 'INV-' . now()->format('Y') . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
            }

            if (empty($invoice->created_by_id)) {
                $invoice->created_by_id = auth()->id() ?? User::value('id');
            }

            $subtotal = (float) $invoice->subtotal;
            $tax = (float) $invoice->tax_amount;
            $discount = (float) $invoice->discount_amount;
            $paid = (float) $invoice->paid_amount;
            $total = max(0, $subtotal + $tax - $discount);

            if (empty($invoice->total_amount)) {
                $invoice->total_amount = $total;
            }

            if ($invoice->balance_due === null) {
                $invoice->balance_due = max(0, $total - $paid);
            }
        });
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
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
