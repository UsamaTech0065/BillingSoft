<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page { margin: 0; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.5;
            background: #ffffff;
        }

        /* ── Brand palette (dompdf-safe solids) ── */
        /* Primary   #4169E2  Royal Blue   */
        /* Deep      #3451B2  Navy Blue    */
        /* Violet    #6366F1  Accent       */
        /* Teal      #0D9488  Secondary    */
        /* Emerald   #059669  Paid/Success */
        /* Amber     #D97706  Partial      */
        /* Coral     #EA580C  Highlight    */

        /* Watermark */
        .watermark {
            position: fixed;
            top: 240px;
            left: 0;
            width: 100%;
            text-align: center;
        }
        .watermark img {
            width: 480px;
            height: 480px;
            opacity: 0.05;
        }
        .watermark-fallback {
            font-size: 120px;
            font-weight: bold;
            color: #4169E2;
            opacity: 0.05;
            letter-spacing: 10px;
        }

        /* ── Header (original white style) ── */
        .pdf-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 120px;
            background: #ffffff;
            border-bottom: 2px solid #4169E2;
        }

        .header-accent {
            height: 4px;
            background-color: #4169E2;
        }

        .header-inner {
            padding: 12px 45px 10px 45px;
        }

        .header-table { width: 100%; border-collapse: collapse; border-spacing: 0; }
        .header-table td { vertical-align: middle; padding: 0; }

        .logo-cell { width: 110px; padding: 0; padding-right: 14px; vertical-align: middle; }

        .logo-wrap {
            width: 110px;
            height: 110px;
            text-align: left;
            line-height: 110px;
        }

        .logo-wrap img {
            display: block;
            max-width: 110px;
            max-height: 110px;
            margin: 0;
        }

        .logo-fallback {
            width: 90px;
            height: 90px;
            background-color: #4169E2;
            border-radius: 12px;
            text-align: center;
            line-height: 90px;
            color: #ffffff;
            font-size: 36px;
            font-weight: bold;
        }

        .co-name {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 2px;
        }

        .co-tagline {
            font-size: 8px;
            color: #4169E2;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 4px;
        }

        .co-quick {
            font-size: 8px;
            color: #64748b;
        }

        .inv-cell { text-align: right; width: 180px; }

        .inv-title {
            font-size: 26px;
            font-weight: bold;
            color: #4169E2;
            letter-spacing: 4px;
            line-height: 1;
        }

        .inv-badge {
            display: inline-block;
            background-color: #E8EDFC;
            color: #3451B2;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            margin-top: 5px;
        }

        /* ── Body ── */
        .pdf-body {
            margin-top: 128px;
            margin-bottom: 250px;
            padding: 0 45px;
        }

        /* Meta cards */
        .meta-row {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .meta-cell {
            width: 25%;
            padding: 0;
            vertical-align: top;
            border: none;
        }

        .meta-cell + .meta-cell {
            padding-left: 8px;
        }

        .meta-card {
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .meta-card-blue   { background-color: #EEF2FF; border-color: #C7D2FE; }
        .meta-card-amber  { background-color: #FFFBEB; border-color: #FDE68A; }
        .meta-card-green  { background-color: #ECFDF5; border-color: #A7F3D0; }
        .meta-card-violet { background-color: #F5F3FF; border-color: #DDD6FE; }
        .meta-card-slate  { background-color: #F8FAFC; border-color: #E2E8F0; }
        .meta-card-orange { background-color: #FFF7ED; border-color: #FED7AA; }
        .meta-card-red    { background-color: #FEF2F2; border-color: #FECACA; }

        .meta-icon {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-bottom: 4px;
        }

        .meta-icon-blue   { color: #4169E2; }
        .meta-icon-amber  { color: #D97706; }
        .meta-icon-green  { color: #059669; }
        .meta-icon-violet { color: #6366F1; }
        .meta-icon-slate  { color: #64748B; }
        .meta-icon-orange { color: #EA580C; }
        .meta-icon-red    { color: #DC2626; }

        .meta-value {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
        }

        .status-draft     { color: #64748b; }
        .status-sent      { color: #4169E2; }
        .status-paid      { color: #059669; }
        .status-cancelled { color: #DC2626; }

        .pay-unpaid  { color: #DC2626; }
        .pay-partial { color: #D97706; }
        .pay-paid    { color: #059669; }

        /* Bill To — aligned with meta cards */
        .bill-to-row {
            width: 100%;
            margin-bottom: 22px;
            border-collapse: collapse;
        }

        .bill-to-cell {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        .bill-to-box {
            background-color: #EEF2FF;
            border: 1px solid #C7D2FE;
            border-left: 5px solid #4169E2;
            border-radius: 10px;
            padding: 14px 18px;
        }

        .section-label {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.8px;
            margin-bottom: 8px;
            color: #4169E2;
        }

        .party-name {
            font-size: 14px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }

        .party-detail {
            font-size: 8.5px;
            color: #475569;
            line-height: 1.7;
        }

        /* Items table — wrapper gives rounded border in dompdf */
        .items-table-wrap {
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #C7D2FE;
            border-radius: 10px;
            overflow: hidden;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .items-table thead tr { background-color: #4169E2; }

        .items-table th {
            padding: 10px 12px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff;
            text-align: left;
            border: none;
        }

        .items-table th:first-child { border-top-left-radius: 10px; }
        .items-table th:last-child  { border-top-right-radius: 10px; }

        .items-table th.r { text-align: right; }

        .items-table tbody tr { border-bottom: 1px solid #E2E8F0; }
        .items-table tbody tr:last-child { border-bottom: none; }
        .items-table tbody tr:nth-child(odd)  { background-color: #ffffff; }
        .items-table tbody tr:nth-child(even) { background-color: #F8FAFF; }

        .items-table td {
            padding: 10px 12px;
            font-size: 9px;
            vertical-align: middle;
        }

        .items-table td.r { text-align: right; }

        .item-name {
            font-weight: bold;
            color: #0f172a;
            font-size: 9.5px;
        }

        .item-desc {
            color: #94a3b8;
            font-size: 7.5px;
            margin-top: 2px;
        }

        .type-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .type-service { background-color: #EEF2FF; color: #4169E2; border: 1px solid #C7D2FE; }
        .type-good    { background-color: #FFF7ED; color: #EA580C; border: 1px solid #FED7AA; }

        .amount-cell {
            font-weight: bold;
            color: #3451B2;
            font-size: 9.5px;
        }

        /* Bottom section */
        .bottom-section { width: 100%; border-collapse: collapse; }
        .bottom-section td { vertical-align: top; }

        .totals-box {
            width: 255px;
            border: 1px solid #C7D2FE;
            border-radius: 10px;
            overflow: hidden;
        }

        .totals-head {
            background-color: #4169E2;
            color: #ffffff;
            padding: 9px 16px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .totals-body {
            padding: 12px 16px;
            background-color: #F8FAFF;
        }

        .totals-table { width: 100%; border-collapse: collapse; }

        .totals-table td {
            padding: 5px 0;
            font-size: 9px;
        }

        .t-label { color: #64748b; }
        .t-value { text-align: right; font-weight: bold; color: #0f172a; }

        .total-row td {
            padding-top: 7px;
            border-top: 1px solid #C7D2FE;
            font-weight: bold;
        }

        .paid-row td { color: #059669; }
        .paid-value { color: #059669; text-align: right; font-weight: bold; }

        .grand-row td {
            padding-top: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .balance-bar {
            background-color: #4169E2;
            color: #ffffff;
            padding: 8px 12px;
            margin-top: 8px;
            border-radius: 8px;
        }

        .balance-bar-paid { background-color: #059669; }

        .balance-bar td {
            padding: 0;
            font-size: 11px;
            font-weight: bold;
            color: #ffffff;
        }

        .balance-bar .bal-val {
            text-align: right;
            font-size: 13px;
        }

        /* Notes */
        .notes-box {
            background-color: #FFFBEB;
            border: 1px solid #FDE68A;
            border-left: 5px solid #F59E0B;
            padding: 12px 16px;
            margin-top: 14px;
            border-radius: 0 8px 8px 0;
        }

        .notes-title {
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #D97706;
            margin-bottom: 5px;
        }

        .notes-text { font-size: 8.5px; color: #92400E; line-height: 1.65; }

        /* Terms & Conditions — pinned above footer */
        .terms-box {
            position: fixed;
            bottom: 92px;
            left: 45px;
            right: 45px;
            border: 1px solid #C7D2FE;
            border-radius: 10px;
            overflow: hidden;
            background-color: #ffffff;
        }

        .terms-head {
            background-color: #EEF2FF;
            border-bottom: 1px solid #C7D2FE;
            padding: 8px 14px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #4169E2;
        }

        .terms-body {
            padding: 8px 12px 10px 12px;
            background-color: #ffffff;
        }

        .terms-list {
            margin: 0;
            padding: 0 0 0 14px;
        }

        .terms-list li {
            font-size: 7px;
            color: #475569;
            line-height: 1.5;
            margin-bottom: 2px;
        }

        .terms-list li:last-child {
            margin-bottom: 0;
        }

        /* Payments */
        .payments-section {
            margin-top: 18px;
            border: 1px solid #99F6E4;
            border-radius: 10px;
            overflow: hidden;
        }

        .payments-head {
            background-color: #0D9488;
            padding: 8px 14px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff;
        }

        .payments-table { width: 100%; border-collapse: collapse; }

        .payments-table th {
            padding: 7px 12px;
            font-size: 7px;
            font-weight: bold;
            text-transform: uppercase;
            color: #0D9488;
            text-align: left;
            background-color: #F0FDFA;
            border-bottom: 1px solid #99F6E4;
        }

        .payments-table th.r { text-align: right; }

        .payments-table td {
            padding: 7px 12px;
            font-size: 8.5px;
            color: #334155;
            border-bottom: 1px solid #CCFBF1;
        }

        .payments-table tr:nth-child(even) td { background-color: #F0FDFA; }
        .payments-table td.r { text-align: right; font-weight: bold; color: #0D9488; }

        /* System note above footer */
        .system-note {
            position: fixed;
            bottom: 62px;
            left: 0;
            right: 0;
            background-color: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 8px 45px;
            text-align: center;
        }

        .system-note-text {
            font-size: 8.5px;
            color: #000000;
            font-style: italic;
            font-weight: bold;
            letter-spacing: 0.2px;
        }

        /* Footer (original white style) */
        .pdf-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 62px;
            background-color: #f8fafc;
            border-top: 2px solid #e2e8f0;
        }

        .footer-accent { height: 3px; background-color: #4169E2; }

        .footer-inner { padding: 8px 45px; }

        .footer-table { width: 100%; border-collapse: collapse; }

        .footer-table td {
            vertical-align: middle;
            font-size: 7.5px;
            color: #64748b;
        }

        .footer-msg {
            font-size: 9px;
            font-weight: bold;
            color: #4169E2;
        }

        .footer-center { text-align: center; }

        .footer-right { text-align: right; color: #94a3b8; }

        .footer-co-name {
            font-weight: bold;
            color: #475569;
            font-size: 8px;
        }
    </style>
</head>
<body>

    {{-- Watermark --}}
    @if($company->getLogoBase64())
        <div class="watermark">
            <img src="{{ $company->getLogoBase64() }}" alt="" style="opacity: 0.05; width: 480px; height: 480px;">
        </div>
    @else
        <div class="watermark">
            <div class="watermark-fallback">{{ strtoupper(substr($company->company_name, 0, 2)) }}</div>
        </div>
    @endif

    {{-- HEADER --}}
    <div class="pdf-header">
        <div class="header-accent"></div>
        <div class="header-inner">
            <table class="header-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="logo-cell">
                        @if($company->getLogoBase64())
                            <div class="logo-wrap">
                                <img src="{{ $company->getLogoBase64() }}" alt="Logo">
                            </div>
                        @else
                            <div class="logo-fallback">{{ strtoupper(substr($company->company_name, 0, 1)) }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="co-name">{{ $company->company_name }}</div>
                        @if($company->tagline)
                            <div class="co-tagline">{{ $company->tagline }}</div>
                        @endif
                        <div class="co-quick">
                            @if($company->email){{ $company->email }}@endif
                            @if($company->email && $company->phone) &nbsp;&bull;&nbsp; @endif
                            @if($company->phone){{ $company->phone }}@endif
                            @if($company->website)
                                @if($company->email || $company->phone) &nbsp;&bull;&nbsp; @endif
                                {{ $company->website }}
                            @endif
                        </div>
                    </td>
                    <td class="inv-cell">
                        <div class="inv-title">INVOICE#</div>
                        <div class="inv-badge">{{ $invoice->invoice_number }}</div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- BODY --}}
    <div class="pdf-body">

        {{-- Meta cards --}}
        <table class="meta-row">
            <tr>
                <td class="meta-cell">
                    <div class="meta-card meta-card-blue">
                        <div class="meta-icon meta-icon-blue">Issue Date</div>
                        <div class="meta-value">{{ $invoice->issue_date->format('M d, Y') }}</div>
                    </div>
                </td>
                <td class="meta-cell">
                    <div class="meta-card meta-card-amber">
                        <div class="meta-icon meta-icon-amber">Due Date</div>
                        <div class="meta-value">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'Upon Receipt' }}</div>
                    </div>
                </td>
                <td class="meta-cell">
                    <div class="meta-card {{ $paymentStatus === 'paid' ? 'meta-card-green' : ($paymentStatus === 'partial' ? 'meta-card-orange' : 'meta-card-red') }}">
                        <div class="meta-icon {{ $paymentStatus === 'paid' ? 'meta-icon-green' : ($paymentStatus === 'partial' ? 'meta-icon-orange' : 'meta-icon-red') }}">Payment</div>
                        <div class="meta-value pay-{{ $paymentStatus }}">{{ ucfirst($paymentStatus) }}</div>
                    </div>
                </td>
                <td class="meta-cell">
                    <div class="meta-card meta-card-violet">
                        <div class="meta-icon meta-icon-violet">Invoice Status</div>
                        <div class="meta-value status-{{ $invoice->status }}">{{ ucfirst($invoice->status) }}</div>
                    </div>
                </td>
            </tr>
        </table>
        {{-- Bill To --}}
        <table class="bill-to-row">
            <tr>
                <td class="bill-to-cell">
                    <div class="bill-to-box">
                        <div class="section-label">Bill To</div>
                        <div class="party-name">{{ $invoice->customer_name }}</div>
                        @if($invoice->customer_email)
                            <div class="party-detail">{{ $invoice->customer_email }}</div>
                        @endif
                        @if($invoice->customer_address)
                            <div class="party-detail">{!! nl2br(e($invoice->customer_address)) !!}</div>
                        @endif
                    </div>
                </td>
                <td></td>
            </tr>
        </table>
        {{-- Line Items — above Bill To, full width aligned left --}}
        <div class="items-table-wrap">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 38%;">Description</th>
                        <th style="width: 12%;">Type</th>
                        <th class="r" style="width: 10%;">Qty</th>
                        <th class="r" style="width: 18%;">Unit Price</th>
                        <th class="r" style="width: 22%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->product->name }}</div>
                            @if($item->description)
                                <div class="item-desc">{{ $item->description }}</div>
                            @elseif($item->product->description)
                                <div class="item-desc">{{ Str::limit($item->product->description, 80) }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="type-badge {{ $item->product->type === 'service' ? 'type-service' : 'type-good' }}">
                                {{ $item->product->type === 'service' ? 'Service' : 'Good' }}
                            </span>
                        </td>
                        <td class="r">{{ $item->quantity }}</td>
                        <td class="r">{{ $company->formatMoney($item->unit_price) }}</td>
                        <td class="r amount-cell">{{ $company->formatMoney($item->total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

      

        {{-- Totals --}}
        <table class="bottom-section">
            <tr>
                <td>
                    @if($invoice->notes)
                    <div class="notes-box">
                        <div class="notes-title">Notes &amp; Terms</div>
                        <div class="notes-text">{!! nl2br(e($invoice->notes)) !!}</div>
                    </div>
                    @endif
                </td>
                <td style="width: 260px; text-align: right;">
                    <div class="totals-box">
                        <div class="totals-head">Invoice Summary</div>
                        <div class="totals-body">
                            <table class="totals-table">
                                <tr>
                                    <td class="t-label">Subtotal</td>
                                    <td class="t-value">{{ $company->formatMoney($invoice->subtotal) }}</td>
                                </tr>
                                @if($invoice->tax_rate > 0)
                                <tr>
                                    <td class="t-label">Tax ({{ number_format($invoice->tax_rate, 2) }}%)</td>
                                    <td class="t-value">{{ $company->formatMoney($invoice->tax_amount) }}</td>
                                </tr>
                                @endif
                                <tr class="total-row">
                                    <td class="t-label" style="color: #3451B2;">Invoice Total</td>
                                    <td class="t-value" style="color: #3451B2;">{{ $company->formatMoney($invoice->total) }}</td>
                                </tr>
                                <tr class="paid-row">
                                    <td class="t-label">Amount Paid</td>
                                    <td class="paid-value">{{ $company->formatMoney($amountPaid) }}</td>
                                </tr>
                            </table>
                            <table class="balance-bar {{ $balanceDue <= 0 ? 'balance-bar-paid' : '' }}" style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td>Balance Due</td>
                                    <td class="bal-val">{{ $company->formatMoney($balanceDue) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- @if($invoice->payments->isNotEmpty())
        <div class="payments-section">
            <div class="payments-head">Payment History ({{ $invoice->payments->count() }})</div>
            <table class="payments-table">
                <thead>
                    <tr>
                        <th style="width: 22%;">Date</th>
                        <th style="width: 18%;">Method</th>
                        <th style="width: 35%;">Reference</th>
                        <th class="r" style="width: 25%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments->sortBy('payment_date') as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td>{{ $payment->getMethodLabel() }}</td>
                        <td>{{ $payment->reference ?: '—' }}</td>
                        <td class="r">{{ $company->formatMoney($payment->amount) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif -->

    </div>

    {{-- Terms & Conditions (bottom of page) --}}
    <div class="terms-box">
        <div class="terms-head">Terms &amp; Conditions</div>
        <div class="terms-body">
            <ol class="terms-list">
                <li>50% advance payment is required before project commencement unless otherwise agreed in writing.</li>
                <li>Remaining balance is due within 7 days of invoice issuance.</li>
                <li>Any work outside the agreed scope will be billed separately.</li>
                <li>Clients must provide required content, approvals, and account access on time.</li>
                <li>Ad spend, third party costs, and production expenses are not included unless stated.</li>
                <li>All payments are non refundable once work has commenced.</li>
                <li>SH Marketing Services does not guarantee specific marketing results or sales.</li>
                <li>Ownership of final deliverables transfers to the client upon full payment.</li>
                <li>Late payments may result in service suspension.</li>
                <li>Payment of this invoice confirms acceptance of these terms.</li>
            </ol>
        </div>
    </div>

    {{-- System disclaimer --}}
    <div class="system-note">
        <div class="system-note-text">This is a system generated slip that does not need any signature or stamp.</div>
    </div>

    {{-- FOOTER --}}
    <div class="pdf-footer">
        <div class="footer-accent"></div>
        <div class="footer-inner">
            <table class="footer-table">
                <tr>
                    <td width="33%">
                        <div class="footer-msg">{{ $company->footer_message ?? 'Thank you for your business!' }}</div>
                    </td>
                    <td width="34%" class="footer-center">
                        <div class="footer-co-name">{{ $company->company_name }}</div>
                        @if($company->getAddressLine())
                            {{ $company->getAddressLine() }}
                        @endif
                    </td>
                    <td width="33%" class="footer-right">
                        {{ $invoice->invoice_number }} &nbsp;&bull;&nbsp; Page 1
                    </td>
                </tr>
            </table>
        </div>
    </div>

</body>
</html>
