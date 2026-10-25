<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PdfExportController extends Controller
{
    public function quotation(Quotation $quotation)
    {
        $quotation->load(['client', 'visaCase.service', 'visaCase.country']);

        $pdf = Pdf::loadView('pdf.quotation', [
            'quotation' => $quotation,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("Quotation-{$quotation->quotation_number}.pdf");
    }

    public function invoice(Invoice $invoice)
    {
        $invoice->load(['client', 'visaCase.service', 'visaCase.country']);

        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice,
        ])->setPaper('a4', 'portrait');

        return $pdf->download("Invoice-{$invoice->invoice_number}.pdf");
    }
}
