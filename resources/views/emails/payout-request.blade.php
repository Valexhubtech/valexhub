<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Affiliate Payout Request</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #2563eb; }
        .label { font-weight: 600; color: #666; margin-bottom: 4px; font-size: 12px; text-transform: uppercase; }
        .value { margin-bottom: 14px; font-size: 15px; }
        .amount { font-size: 28px; font-weight: 700; color: #16a34a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payout Request</h1>
        </div>
        <div class="content">
            <div class="info-box">
                <div class="label">Affiliate</div>
                <div class="value">{{ $affiliate->name }} ({{ $affiliate->email }})</div>

                <div class="label">Amount Requested</div>
                <div class="amount">₦{{ number_format($amount, 2) }}</div>

                <div class="label">Bank Name</div>
                <div class="value">{{ $bankDetails['bank_name'] }}</div>

                <div class="label">Account Name</div>
                <div class="value">{{ $bankDetails['account_name'] }}</div>

                <div class="label">Account Number</div>
                <div class="value">{{ $bankDetails['account_number'] }}</div>
            </div>
            <p>Please process this payout within 3–5 business days and mark the commissions as claimed in the admin panel.</p>
        </div>
    </div>
</body>
</html>
