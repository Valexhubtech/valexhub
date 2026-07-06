<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Reactivated</title>
    <style>
        body { margin: 0; padding: 0; background: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }
        .wrapper { max-width: 560px; margin: 40px auto; background: #ffffff; border-radius: 8px; overflow: hidden; border: 1px solid #e4e4e7; }
        .header { background: #18181b; padding: 28px 32px; }
        .header h1 { margin: 0; color: #ffffff; font-size: 20px; font-weight: 600; }
        .header p { margin: 4px 0 0; color: #a1a1aa; font-size: 14px; }
        .body { padding: 32px; }
        .status-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 20px 24px; margin-bottom: 24px; }
        .status-box .label { color: #15803d; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 4px; }
        .status-box .value { color: #166534; font-size: 22px; font-weight: 700; }
        .product-name { color: #18181b; font-size: 16px; font-weight: 600; margin-bottom: 4px; }
        p { color: #52525b; font-size: 14px; line-height: 1.6; margin: 0 0 16px; }
        .cta { display: inline-block; background: #18181b; color: #ffffff; padding: 12px 24px; border-radius: 6px; font-size: 14px; font-weight: 600; text-decoration: none; margin-top: 8px; }
        .footer { padding: 20px 32px; background: #fafafa; border-top: 1px solid #f4f4f5; }
        .footer p { color: #a1a1aa; font-size: 12px; margin: 0; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>ValexHub</h1>
            <p>Service Management</p>
        </div>
        <div class="body">
            <div class="status-box">
                <div class="label">Status</div>
                <div class="value">✓ Active</div>
            </div>

            <p class="product-name">{{ $deployment->product?->name ?? 'Your Service' }}</p>
            <p>Great news! Your payment has been received and your service has been reactivated. You can now access your application as normal.</p>

            @if($deployment->deployment_url)
                <p>Your app is running at:<br>
                    <a href="{{ $deployment->deployment_url }}" style="color: #18181b; font-weight: 600;">{{ $deployment->deployment_url }}</a>
                </p>
            @endif

            <a href="{{ url('/dashboard/deployments/' . $deployment->id) }}" class="cta">View Deployment</a>

            <p style="margin-top: 24px; font-size: 13px; color: #71717a;">
                If you have any questions, reply to this email or visit your support dashboard.
            </p>
        </div>
        <div class="footer">
            <p>ValexHub · Business Software Solutions &bull; This email was sent to {{ $deployment->user->email ?? '' }}</p>
        </div>
    </div>
</body>
</html>
