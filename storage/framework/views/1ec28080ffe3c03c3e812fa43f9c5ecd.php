<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Invoice - <?php echo e($invoice->invoice_number); ?></title>
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
            margin-bottom: 25px;
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
            color: #059669;
            text-transform: uppercase;
        }
        .doc-meta {
            text-align: right;
            font-size: 12px;
            color: #475569;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
        }
        .status-paid { background: #dcfce7; color: #15803d; }
        .status-partial { background: #fef9c3; color: #a16207; }
        .status-sent { background: #e0f2fe; color: #0369a1; }
        .status-overdue { background: #fee2e2; color: #b91c1c; }

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
            width: 48%;
            margin-left: 52%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .totals-table td {
            padding: 6px 12px;
            font-size: 12px;
        }
        .totals-table .balance-row td {
            border-top: 2px solid #0f172a;
            border-bottom: 2px solid #0f172a;
            font-size: 15px;
            font-weight: 800;
            color: #0f172a;
            padding: 10px 12px;
        }
        .payment-box {
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 12px 16px;
            background: #f0fdf4;
            font-size: 11px;
            color: #166534;
            margin-bottom: 25px;
        }
        .signature-table {
            width: 100%;
            margin-top: 30px;
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
                    CR No: 184920 • Regulated Legal & Corporate Services<br>
                    Email: finance@venturevisionary.qa • Web: venturevisionary.qa
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <div class="doc-title">TAX INVOICE</div>
                <div class="doc-meta">
                    <strong>Invoice #:</strong> <?php echo e($invoice->invoice_number); ?><br>
                    <strong>Issue Date:</strong> <?php echo e($invoice->issue_date ? $invoice->issue_date->format('d M Y') : $invoice->created_at->format('d M Y')); ?><br>
                    <strong>Due Date:</strong> <?php echo e($invoice->due_date ? $invoice->due_date->format('d M Y') : 'Due on Receipt'); ?><br>
                    <strong>Status:</strong> 
                    <span class="status-badge status-<?php echo e($invoice->status); ?>"><?php echo e($invoice->status); ?></span>
                </div>
            </td>
        </tr>
    </table>

    <table class="info-grid">
        <tr>
            <td class="info-box">
                <div class="info-title">Billed To (Client / Organization)</div>
                <div class="info-name"><?php echo e($invoice->client->full_name ?? 'Client'); ?></div>
                <div>Passport: <?php echo e($invoice->client->passport_number ?? 'On File'); ?></div>
                <div>QID: <?php echo e($invoice->client->qid_number ?? 'N/A'); ?></div>
                <div>Email: <?php echo e($invoice->client->email ?? 'N/A'); ?></div>
                <div>Phone: <?php echo e($invoice->client->phone ?? 'N/A'); ?></div>
            </td>
            <td style="width: 4%;"></td>
            <td class="info-box">
                <div class="info-title">Service & Case Reference</div>
                <div class="info-name"><?php echo e($invoice->visaCase->service->name ?? 'Immigration & Business Consultancy'); ?></div>
                <div>Case Reference: <?php echo e($invoice->visaCase->case_number ?? 'Direct Billing'); ?></div>
                <div>Country / Track: <?php echo e($invoice->visaCase->country->name ?? 'International'); ?></div>
                <div>Payment Terms: Immediate / Milestone</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 50%;">Description</th>
                <th style="width: 15%;" class="text-right">Unit Price</th>
                <th style="width: 10%;" class="text-right">Qty</th>
                <th style="width: 20%;" class="text-right">Amount (<?php echo e($invoice->currency); ?>)</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!empty($invoice->line_items) && is_array($invoice->line_items)): ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $invoice->line_items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($index + 1); ?></td>
                    <td><strong><?php echo e($item['description'] ?? 'Consultancy Service'); ?></strong></td>
                    <td class="text-right"><?php echo e(number_format((float)($item['unit_price'] ?? 0), 2)); ?></td>
                    <td class="text-right"><?php echo e($item['quantity'] ?? 1); ?></td>
                    <td class="text-right"><?php echo e(number_format((float)($item['total'] ?? 0), 2)); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php else: ?>
                <tr>
                    <td>1</td>
                    <td><strong><?php echo e($invoice->visaCase->service->name ?? 'Immigration & Visa Processing Consultancy'); ?></strong></td>
                    <td class="text-right"><?php echo e(number_format((float)$invoice->subtotal, 2)); ?></td>
                    <td class="text-right">1</td>
                    <td class="text-right"><?php echo e(number_format((float)$invoice->subtotal, 2)); ?></td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    <table class="totals-table">
        <tr>
            <td><strong>Subtotal:</strong></td>
            <td class="text-right"><?php echo e(number_format((float)$invoice->subtotal, 2)); ?> <?php echo e($invoice->currency); ?></td>
        </tr>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->tax_amount > 0): ?>
        <tr>
            <td><strong>Tax / VAT:</strong></td>
            <td class="text-right"><?php echo e(number_format((float)$invoice->tax_amount, 2)); ?> <?php echo e($invoice->currency); ?></td>
        </tr>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if((float) $invoice->discount_amount > 0): ?>
        <tr>
            <td><strong>Discount:</strong></td>
            <td class="text-right" style="color: #16a34a;">-<?php echo e(number_format((float)$invoice->discount_amount, 2)); ?> <?php echo e($invoice->currency); ?></td>
        </tr>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <tr>
            <td><strong>Total Invoice Amount:</strong></td>
            <td class="text-right"><strong><?php echo e(number_format((float)$invoice->total_amount, 2)); ?> <?php echo e($invoice->currency); ?></strong></td>
        </tr>
        <tr>
            <td><strong>Paid to Date:</strong></td>
            <td class="text-right" style="color: #059669;">-<?php echo e(number_format((float)$invoice->paid_amount, 2)); ?> <?php echo e($invoice->currency); ?></td>
        </tr>
        <tr class="balance-row">
            <td><strong>Balance Due:</strong></td>
            <td class="text-right" style="color: <?php echo e((float)$invoice->balance_due > 0 ? '#b91c1c' : '#059669'); ?>;">
                <strong><?php echo e(number_format((float)$invoice->balance_due, 2)); ?> <?php echo e($invoice->currency); ?></strong>
            </td>
        </tr>
    </table>

    <div class="payment-box">
        <strong>Bank Transfer Instructions (Qatar National Bank - QNB):</strong><br>
        • Account Title: Venture Visionary Visa and Business Consultancy W.L.L.<br>
        • Bank: Qatar National Bank (QNB), Main Branch, Doha, Qatar<br>
        • IBAN: QA92 QNBA 0000 0000 1234 5678 9012 3<br>
        • Swift Code: QNBAQAQA<br>
        <em>Please quote invoice number <strong><?php echo e($invoice->invoice_number); ?></strong> as payment reference.</em>
    </div>

    <table class="signature-table">
        <tr>
            <td class="signature-box">
                <strong>Venture Visionary Accounts Department</strong><br>
                Official Electronic Tax Invoice • Computer Generated
            </td>
            <td style="width: 10%;"></td>
            <td class="signature-box">
                <strong>Client Confirmation / Receipt Stamp</strong><br>
                Received By: ________________________
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH /var/www/html/resources/views/pdf/invoice.blade.php ENDPATH**/ ?>