<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoiceNumber() }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #18181b; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
        .amount-box { background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center; }
        .amount { font-size: 32px; font-weight: 700; color: #18181b; }
        .amount-label { font-size: 13px; color: #6b7280; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; margin: 20px 0; }
        th { background: #f3f4f6; padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; }
        th:last-child { text-align: right; }
        td { padding: 11px 14px; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
        td:last-child { text-align: right; font-weight: 600; }
        .total-row td { font-weight: 700; font-size: 15px; border-bottom: none; background: #f9fafb; }
        .meta { font-size: 12px; color: #6b7280; margin-top: 20px; }
        .meta span { display: inline-block; margin-right: 20px; }
        .footer { font-size: 12px; color: #9ca3af; text-align: center; margin-top: 30px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2 style="margin:0 0 4px; font-size:20px;">Payment Receipt</h2>
        <p style="margin:0; opacity:0.8; font-size:13px;">{{ $invoice->invoiceNumber() }}</p>
    </div>

    <div class="content">
        <p>Hi {{ $invoice->user->name }},</p>
        <p>Thank you! We've received your payment and your invoice is attached to this email.</p>

        <div class="amount-box">
            <div class="amount">₦{{ number_format((float) $invoice->amount, 2) }}</div>
            <div class="amount-label">Paid on {{ $invoice->paid_at?->format('d M Y') ?? now()->format('d M Y') }}</div>
        </div>

        <table>
            <thead>
                <tr><th>Description</th><th>Amount</th></tr>
            </thead>
            <tbody>
                @foreach($invoice->line_items as $item)
                <tr>
                    <td>{{ $item['label'] }}</td>
                    <td>₦{{ number_format($item['amount'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td>Total Paid</td>
                    <td>₦{{ number_format((float) $invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="meta">
            <span>Reference: {{ $invoice->paystack_reference ?? '—' }}</span>
            @if($invoice->userProduct?->next_renewal_date)
            <span>Next renewal: {{ $invoice->userProduct->next_renewal_date->format('d M Y') }}</span>
            @endif
        </div>

        <p style="margin-top: 24px;">The full invoice PDF is attached. You can also download it anytime from your <a href="{{ url('/dashboard/deployments') }}" style="color: #18181b;">deployment dashboard</a>.</p>
    </div>

    <div class="footer">
        ValexHub · support@valexhub.com<br>
        {{ $invoice->invoiceNumber() }}
    </div>
</div>
</body>
</html>
