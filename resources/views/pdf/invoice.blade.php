<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoiceNumber() }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 13px; color: #1a1a1a; background: #fff; }
        .container { padding: 48px; max-width: 720px; margin: 0 auto; }

        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; }
        .brand { font-size: 22px; font-weight: 700; color: #111; }
        .brand-sub { font-size: 11px; color: #888; margin-top: 2px; }

        .invoice-meta { text-align: right; }
        .invoice-num { font-size: 18px; font-weight: 700; color: #111; }
        .invoice-status { display: inline-block; margin-top: 4px; padding: 2px 10px; background: #d1fae5; color: #065f46; border-radius: 12px; font-size: 10px; font-weight: 600; text-transform: uppercase; }

        .divider { border: none; border-top: 1px solid #e5e7eb; margin: 28px 0; }

        .parties { display: flex; justify-content: space-between; margin-bottom: 36px; }
        .party-label { font-size: 10px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
        .party-name { font-size: 14px; font-weight: 600; color: #111; }
        .party-detail { font-size: 12px; color: #6b7280; margin-top: 2px; }

        .dates { display: flex; gap: 40px; margin-bottom: 32px; }
        .date-block .date-label { font-size: 10px; font-weight: 600; color: #9ca3af; text-transform: uppercase; }
        .date-block .date-value { font-size: 13px; font-weight: 500; color: #111; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead th { background: #f9fafb; padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        thead th:last-child { text-align: right; }
        tbody td { padding: 12px 12px; border-bottom: 1px solid #f3f4f6; font-size: 13px; color: #374151; vertical-align: top; }
        tbody td:last-child { text-align: right; font-weight: 500; color: #111; }
        .item-type { font-size: 10px; color: #9ca3af; margin-top: 2px; }

        .totals { margin-left: auto; width: 260px; }
        .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; color: #6b7280; }
        .total-row.grand { border-top: 2px solid #111; margin-top: 8px; padding-top: 12px; font-size: 16px; font-weight: 700; color: #111; }

        .footer { margin-top: 48px; padding-top: 24px; border-top: 1px solid #e5e7eb; }
        .footer-text { font-size: 11px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div>
            <div class="brand">ValexHub</div>
            <div class="brand-sub">Business Software Solutions</div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-num">{{ $invoice->invoiceNumber() }}</div>
            <span class="invoice-status">Paid</span>
        </div>
    </div>

    <hr class="divider">

    {{-- Parties --}}
    <div class="parties">
        <div>
            <div class="party-label">From</div>
            <div class="party-name">ValexHub</div>
            <div class="party-detail">info@valexhub.com</div>
        </div>
        <div style="text-align: right;">
            <div class="party-label">Bill To</div>
            <div class="party-name">{{ $invoice->user->name }}</div>
            <div class="party-detail">{{ $invoice->user->email }}</div>
        </div>
    </div>

    {{-- Dates --}}
    <div class="dates">
        <div class="date-block">
            <div class="date-label">Invoice Date</div>
            <div class="date-value">{{ $invoice->created_at->format('d M Y') }}</div>
        </div>
        <div class="date-block">
            <div class="date-label">Paid On</div>
            <div class="date-value">{{ $invoice->paid_at?->format('d M Y') ?? '—' }}</div>
        </div>
        @if($invoice->userProduct?->next_renewal_date)
        <div class="date-block">
            <div class="date-label">Next Renewal</div>
            <div class="date-value">{{ $invoice->userProduct->next_renewal_date->format('d M Y') }}</div>
        </div>
        @endif
        @if($invoice->paystack_reference)
        <div class="date-block">
            <div class="date-label">Reference</div>
            <div class="date-value" style="font-size: 11px; font-family: monospace;">{{ $invoice->paystack_reference }}</div>
        </div>
        @endif
    </div>

    {{-- Line items --}}
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->line_items as $item)
            <tr>
                <td>
                    {{ $item['label'] }}
                    <div class="item-type">{{ ucfirst($item['type']) }}</div>
                </td>
                <td>₦{{ number_format($item['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Total --}}
    <div class="totals">
        <div class="total-row grand">
            <span>Total Paid</span>
            <span>₦{{ number_format((float) $invoice->amount, 2) }}</span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-text">
            Thank you for your business. For queries, contact support@valexhub.com<br>
            {{ $invoice->invoiceNumber() }} · Generated {{ now()->format('d M Y') }}
        </div>
    </div>

</div>
</body>
</html>
