<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>You just earned a new commission!</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #2563eb; }
        .label { font-weight: 600; color: #666; margin-bottom: 5px; }
        .value { margin-bottom: 15px; }
        .amount { font-size: 28px; font-weight: 700; color: #16a34a; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Commission Earned</h1>
        </div>

        <div class="content">
            <div class="info-box">
                <div class="amount">₦{{ number_format($commission->commission_amount, 2) }}</div>

                <div class="label">Referred Client:</div>
                <div class="value">{{ $commission->referredUser->name }}</div>

                <div class="label">Billing Month:</div>
                <div class="value">Month {{ $commission->billing_month_number }}</div>

                <div class="label">Commission Rate:</div>
                <div class="value">{{ number_format($commission->commission_rate, 0) }}%</div>
            </div>

            <p>This has been added to your claimable balance in your Affiliate Tracker dashboard.</p>
        </div>
    </div>
</body>
</html>
