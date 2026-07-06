<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your {{ $deployment->product->name }} deployment is ready</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #16a34a; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #16a34a; }
        .label { font-weight: 600; color: #666; margin-bottom: 5px; }
        .value { margin-bottom: 15px; font-family: 'Courier New', monospace; }
        .btn { display: inline-block; padding: 12px 24px; background: #16a34a; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
        .btn:hover { background: #15803d; }
        .warning { font-size: 13px; color: #92400e; background: #fef3c7; padding: 12px; border-radius: 6px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Your deployment is ready</h1>
            <p>{{ $deployment->product->name }} has been provisioned successfully</p>
        </div>

        <div class="content">
            <div class="info-box">
                <div class="label">Deployment URL:</div>
                <div class="value"><a href="{{ $deployment->deployment_url }}">{{ $deployment->deployment_url }}</a></div>

                <div class="label">Username:</div>
                <div class="value">{{ $credentials['username'] ?? '' }}</div>

                <div class="label">Password:</div>
                <div class="value">{{ $credentials['password'] ?? '' }}</div>

                @if(!empty($credentials['admin_url']))
                <div class="label">Admin URL:</div>
                <div class="value"><a href="{{ $credentials['admin_url'] }}">{{ $credentials['admin_url'] }}</a></div>
                @endif
            </div>

            <a href="{{ $deployment->deployment_url }}" class="btn">Open Your App</a>

            <p class="warning">Keep these credentials safe — this is the only place they're shown in full. You can also log in any time from your dashboard's One-Click Login button without re-entering them.</p>
        </div>
    </div>
</body>
</html>
