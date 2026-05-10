<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Thank you for your {{ ucfirst($demoRequest->request_type) }} Request</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #10b981; }
        .label { font-weight: 600; color: #666; margin-bottom: 5px; }
        .value { margin-bottom: 15px; }
        .highlight { background: #dcfce7; padding: 15px; border-radius: 6px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Thank You!</h1>
            <p>We've received your {{ $demoRequest->request_type }} request</p>
        </div>
        
        <div class="content">
            <p>Hi {{ $demoRequest->name }},</p>
            
            <p>Thank you for your interest in <strong>{{ $demoRequest->product->name }}</strong>! We've successfully received your {{ $demoRequest->request_type }} request.</p>
            
            <div class="info-box">
                <div class="label">Request Details:</div>
                <div class="value">
                    <strong>Product:</strong> {{ $demoRequest->product->name }}<br>
                    <strong>Request Type:</strong> {{ ucfirst($demoRequest->request_type) }}<br>
                    <strong>Submitted:</strong> {{ $demoRequest->created_at->format('M j, Y \a\t g:i A') }}<br>
                    @if($demoRequest->referral_code)
                    <strong>Referral Code:</strong> {{ $demoRequest->referral_code }}
                    @endif
                </div>
            </div>
            
            <div class="highlight">
                <strong>What happens next?</strong><br>
                Our team will review your request and get back to you within 24 hours. We'll contact you at {{ $demoRequest->email }} or {{ $demoRequest->phone }} to schedule your {{ $demoRequest->request_type === 'demo' ? 'demo session' : 'quote discussion' }}.
            </div>
            
            <p>In the meantime, feel free to explore our other products and services on our website.</p>
            
            <p>Best regards,<br>
            The {{ config('app.name') }} Team</p>
        </div>
    </div>
</body>
</html>