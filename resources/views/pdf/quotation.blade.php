<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quotation - {{ $quotation->quotation_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 30px;
            font-size: 13px;
            line-height: 1.5;
        }
        .header-table {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #0f172a;
            padding-bottom: 20px;
        }
        .logo-text {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }
        .tagline {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .doc-title {
            text-align: right;
            font-size: 24px;
            font-weight: 800;
            color: #0284c7;
            text-transform: uppercase;
        }
        .doc-meta {
            text-align: right;
            font-size: 12px;
            color: #475569;
        }
        .info-grid {
            width: 100%;
            margin-bottom: 25px;
        }
        .info-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            vertical-align: top;
            width: 48%;
        }
        .info-title {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            margin-bottom: 6px;
        }
        .info-name {
            font-size: 14px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background: #0f172a;
            color: #ffffff;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 10px 14px;
            text-align: left;
        }
        .items-table td {
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 12px;
        }
        .items-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .text-right {
            text-align: right;
        }
        .totals-table {
            width: 45%;
            margin-left: 55%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .totals-table td {
            padding: 6px 12px;
            font-size: 12px;
        }
        .totals-table .grand-total td {
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            font-size: 15px;
            font-weight: 800;
            color: #0284c7;
            padding: 10px 12px;
        }
        .terms-box {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 12px 16px;
            background: #f1f5f9;
            font-size: 11px;
            color: #475569;
            margin-bottom: 30px;
        }
        .signature-table {
            width: 100%;
            margin-top: 40px;
        }
        .signature-box {
            width: 45%;
            border-top: 1px dashed #94a3b8;
            padding-top: 8px;
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div class="logo-text">Venture Visionary</div>
                <div class="tagline">Visa & Business Consultancy • Doha, Qatar</div>
                <div style="font-size: 11px; color: #64748b; margin-top: 4px;">
                    Al Reem Tower, West Bay, Doha, State of Qatar<br>
                    CR No: 184920 • Regulated Immigration Services<br>
                    Email: support@venturevisionary.qa • Web: venturevisionary.qa
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <div class="doc-title">QUOTATION</div>
                <div class="doc-meta">
                    <strong>Ref #:</strong> {{ $quotation->quotation_number }}<br>
                    <strong>Issue Date:</strong> {{ $quotation->created_at->format('d M Y') }}<br>
                    <strong>Valid Until:</strong> {{ $quotation->valid_until ? $quotation->valid_until->format('d M Y') : '14 Days from issue' }}<br>
                    <strong>Currency:</strong> {{ $quotation->currency }}<br>
                    <strong>Status:</strong> <span style="text-transform: uppercase; font-weight: bold;">{{ $quotation->status }}</span>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-grid">
        <tr>
            <td class="info-box">
                <div class="info-title">Prepared For (Client)</div>
                <div class="info-name">{{ $quotation->client->full_name ?? 'Valued Client' }}</div>
                <div>Passport: {{ $quotation->client->passport_number ?? 'On File' }}</div>
                <div>QID: {{ $quotation->client->qid_number ?? 'N/A' }}</div>
                <div>Email: {{ $quotation->client->email ?? 'N/A' }}</div>
                <div>Phone: {{ $quotation->client->phone ?? 'N/A' }}</div>
            </td>
            <td style="width: 4%;"></td>
            <td class="info-box">
                <div class="info-title">Consultancy & Case Scope</div>
                <div class="info-name">{{ $quotation->visaCase->service->name ?? 'Immigration & Consultancy Services' }}</div>
                <div>Destination: {{ $quotation->visaCase->country->name ?? 'International Destination' }}</div>
                <div>Case Ref: {{ $quotation->visaCase->case_number ?? 'New Initiation' }}</div>
                <div>Advisory Track: Comprehensive Preparation & Filing</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 55%;">Item / Service Description</th>
                <th style="width: 20%;" class="text-right">Category</th>
                <th style="width: 20%;" class="text-right">Amount ({{ $quotation->currency }})</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <strong>Embassy / Official Government Fee</strong><br>
                    <span style="font-size: 11px; color: #64748b;">Direct consular application processing fee payable to diplomatic mission.</span>
                </td>
                <td class="text-right">Government Fee</td>
                <td class="text-right">{{ number_format($quotation->government_fee, 2) }}</td>
            </tr>
            <tr>
                <td>2</td>
                <td>
                    <strong>Visa Application Center (VAC) & Biometrics Charges</strong><br>
                    <span style="font-size: 11px; color: #64748b;">VFS Global / TLScontact appointment, slot booking & biometrics handling.</span>
                </td>
                <td class="text-right">VAC Facility</td>
                <td class="text-right">{{ number_format($quotation->vac_fee, 2) }}</td>
            </tr>
            <tr>
                <td>3</td>
                <td>
                    <strong>Venture Visionary Professional Legal & Consultancy Fee</strong><br>
                    <span style="font-size: 11px; color: #64748b;">Eligibility audit, cover letter drafting, dynamic checklist review, and case management.</span>
                </td>
                <td class="text-right">Consultancy</td>
                <td class="text-right">{{ number_format($quotation->consultancy_fee, 2) }}</td>
            </tr>
            @if((float) $quotation->other_charges > 0)
            <tr>
                <td>4</td>
                <td>
                    <strong>Certified Translations, Legalization & Logistics</strong><br>
                    <span style="font-size: 11px; color: #64748b;">Document attestations, certified legal translations, and international courier fees.</span>
                </td>
                <td class="text-right">Ancillary</td>
                <td class="text-right">{{ number_format($quotation->other_charges, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td><strong>Subtotal:</strong></td>
            <td class="text-right">{{ number_format((float)$quotation->government_fee + (float)$quotation->vac_fee + (float)$quotation->consultancy_fee + (float)$quotation->other_charges, 2) }} {{ $quotation->currency }}</td>
        </tr>
        @if((float) $quotation->discount_amount > 0)
        <tr>
            <td><strong>Promotional Discount:</strong></td>
            <td class="text-right" style="color: #16a34a;">-{{ number_format($quotation->discount_amount, 2) }} {{ $quotation->currency }}</td>
        </tr>
        @endif
        <tr class="grand-total">
            <td><strong>Total Investment:</strong></td>
            <td class="text-right"><strong>{{ number_format($quotation->total_amount, 2) }} {{ $quotation->currency }}</strong></td>
        </tr>
    </table>

    <div class="terms-box">
        <strong>Terms & Conditions (Rule 10: Transparency Protocol):</strong><br>
        1. Official embassy & VAC fees are non-refundable once scheduled or lodged with the diplomatic authorities.<br>
        2. Venture Visionary provides expert procedural and compliance representation; official granting decisions rest solely with the sovereign embassy/consulate.<br>
        3. Quotation is valid for 14 calendar days from issue. Case initiation commences immediately upon deposit confirmation.
    </div>

    <table class="signature-table">
        <tr>
            <td class="signature-box">
                <strong>Venture Visionary Consultancy</strong><br>
                Authorized Commercial Signatory & Seal
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box">
                <strong>Client Acceptance Signature</strong><br>
                Name: {{ $quotation->client->full_name ?? 'Client' }}<br>
                Date: ________________________
            </td>
        </tr>
    </table>
</body>
</html>
