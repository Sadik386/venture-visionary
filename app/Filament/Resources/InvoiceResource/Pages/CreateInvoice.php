<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by_id'] = Auth::id();

        // Auto-generate invoice number
        $year  = now()->format('Y');
        $count = \App\Models\Invoice::whereYear('created_at', $year)->count() + 1;
        $data['invoice_number'] = 'INV-' . $year . '-' . str_pad((string) $count, 4, '0', STR_PAD_LEFT);

        // Calculate balance_due
        $subtotal  = (float) ($data['subtotal'] ?? 0);
        $tax       = (float) ($data['tax_amount'] ?? 0);
        $discount  = (float) ($data['discount_amount'] ?? 0);
        $paid      = (float) ($data['paid_amount'] ?? 0);
        $total     = max(0, $subtotal + $tax - $discount);

        $data['total_amount'] = $total;
        $data['balance_due']  = max(0, $total - $paid);

        return $data;
    }
}
