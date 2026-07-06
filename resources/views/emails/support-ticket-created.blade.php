<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #18181b; color: white; padding: 24px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 24px; border-radius: 0 0 8px 8px; }
        .message-box { background: white; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; margin: 16px 0; }
        .meta { font-size: 12px; color: #6b7280; }
        .btn { display: inline-block; padding: 10px 20px; background: #18181b; color: white; text-decoration: none; border-radius: 6px; font-size: 13px; margin-top: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2 style="margin:0 0 4px;">New Support Ticket</h2>
        <p style="margin:0; opacity:0.7; font-size:13px;">Ticket #{{ $ticket->id }}</p>
    </div>
    <div class="content">
        <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
        <p><strong>From:</strong> {{ $ticket->user->name }} ({{ $ticket->user->email }})</p>
        @if($ticket->deployment)
            <p><strong>Deployment:</strong> {{ $ticket->deployment->product->name ?? 'Unknown' }} (#{{ $ticket->deployment->id }})</p>
        @endif
        <div class="message-box">
            <p class="meta">Client message:</p>
            <p style="margin: 8px 0 0;">{{ $messageBody }}</p>
        </div>
        <a href="{{ config('app.url') }}/admin/support-tickets/{{ $ticket->id }}/edit" class="btn">View in Admin</a>
    </div>
</div>
</body>
</html>
