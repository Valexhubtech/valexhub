<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New {{ ucfirst($demoRequest->request_type) }} Request</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2563eb; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 8px 8px; }
        .info-box { background: white; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #2563eb; }
        .label { font-weight: 600; color: #666; margin-bottom: 5px; }
        .value { margin-bottom: 15px; }
        .btn { display: inline-block; padding: 12px 24px; background: #2563eb; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
        .btn:hover { background: #1d4ed8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New {{ ucfirst($demoRequest->request_type) }} Request</h1>
            <p>A new {{ $demoRequest->request_type }} request has been submitted</p>
        </div>
        
        <div class="content">
            <div class="info-box">
                <div class="label">Product:</div>
                <div class="value">{{ $demoRequest->product->name }}</div>
                
                <div class="label">Customer Name:</div>
                <div class="value">{{ $demoRequest->name }}</div>
                
                <div class="label">Email:</div>
                <div class="value">{{ $demoRequest->email }}</div>
                
                <div class="label">Phone:</div>
                <div class="value">{{ $demoRequest->phone }}</div>
                
                @if($demoRequest->referral_code)
                <div class="label">Referral Code:</div>
                <div class="value">{{ $demoRequest->referral_code }}</div>
                @endif
                
                <div class="label">Request Type:</div>
                <div class="value">{{ ucfirst($demoRequest->request_type) }}</div>
                
                <div class="label">Submitted:</div>
                <div class="value">{{ $demoRequest->created_at->format('M j, Y \a\t g:i A') }}</div>
            </div>
            
            <p>Please follow up with this customer promptly.</p>
            
            <a href="{{ $demoRequest->generateWhatsAppUrl() }}" class="btn">Contact via WhatsApp</a>
        </div>
    </div>
</body>
</html>