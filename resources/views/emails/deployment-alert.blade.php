<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #dc2626; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .header.stopped { background: #d97706; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #dc2626; }
        .label { font-weight: 600; color: #666; font-size: 13px; margin-bottom: 3px; }
        .value { margin-bottom: 12px; }
        .footer { font-size: 12px; color: #999; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>
<div class="container">

    @if ($audience === 'support')
        <div class="header {{ in_array($reason, ['stopped', 'crashed']) ? 'stopped' : '' }}">
            <h1 style="margin:0">⚠️ Deployment Alert</h1>
            <p style="margin:8px 0 0">Needs attention</p>
        </div>
        <div class="content">
            <p><strong>Issue:</strong> {{ $reason }}</p>
            <div class="info-box">
                <div class="label">Customer</div>
                <div class="value">{{ $deployment->user->name ?? '—' }} ({{ $deployment->user->email ?? '—' }})</div>
                <div class="label">Business</div>
                <div class="value">{{ $deployment->business_name ?? '—' }}</div>
                <div class="label">Product</div>
                <div class="value">{{ $deployment->product->name ?? '—' }}</div>
                <div class="label">Deployment ID</div>
                <div class="value">#{{ $deployment->id }}</div>
                @if ($deployment->coolify_app_id)
                    <div class="label">Coolify App UUID</div>
                    <div class="value" style="font-family: monospace; font-size: 13px;">{{ $deployment->coolify_app_id }}</div>
                @endif
                @if ($deployment->deployment_url)
                    <div class="label">App URL</div>
                    <div class="value"><a href="{{ $deployment->deployment_url }}">{{ $deployment->deployment_url }}</a></div>
                @endif
            </div>
            <p>Log in to the admin dashboard to investigate and take action.</p>
        </div>

    @else
        @php
            $isDown = in_array($reason, ['stopped', 'crashed', 'failed']);
        @endphp
        <div class="header {{ $isDown ? 'stopped' : '' }}">
            <h1 style="margin:0">Your deployment needs attention</h1>
            <p style="margin:8px 0 0">{{ $deployment->product->name ?? 'ValexHub' }}</p>
        </div>
        <div class="content">
            @if ($reason === 'failed')
                <p>We're sorry — your deployment failed to start. Our team has been notified and will investigate immediately.</p>
                <p>You have not been charged for this deployment attempt. Please contact support if you have any questions.</p>
            @elseif ($reason === 'stopped')
                <p>Your application has stopped unexpectedly. Our team has been alerted and is investigating.</p>
                <p>If this is urgent, please contact our support team and reference your deployment ID below.</p>
            @elseif ($reason === 'crashed')
                <p>Your application has crashed and has reached its automatic restart limit. Our team has been notified.</p>
                <p>Please contact support so we can restore your service as quickly as possible.</p>
            @else
                <p>There's an issue with your deployment. Our support team has been notified and will be in touch shortly.</p>
            @endif

            <div class="info-box">
                <div class="label">Product</div>
                <div class="value">{{ $deployment->product->name ?? '—' }}</div>
                <div class="label">Deployment ID</div>
                <div class="value">#{{ $deployment->id }}</div>
                @if ($deployment->deployment_url)
                    <div class="label">App URL</div>
                    <div class="value"><a href="{{ $deployment->deployment_url }}">{{ $deployment->deployment_url }}</a></div>
                @endif
            </div>

            <p>If you need immediate help, reply to this email or visit your client area.</p>
        </div>
    @endif

    <div class="footer">
        &copy; {{ date('Y') }} ValexHub. All rights reserved.
    </div>
</div>
</body>
</html>
