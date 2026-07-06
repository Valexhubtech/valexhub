<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #18181b; color: white; padding: 24px; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 24px; border-radius: 0 0 8px 8px; }
        .message-box { background: white; border-left: 4px solid #18181b; padding: 16px; margin: 16px 0; border-radius: 0 8px 8px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #18181b; color: white; text-decoration: none; border-radius: 6px; font-size: 13px; margin-top: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2 style="margin:0 0 4px;">ValexHub Support Reply</h2>
        <p style="margin:0; opacity:0.7; font-size:13px;">Re: {{ $ticket->subject }}</p>
    </div>
    <div class="content">
        <p>Hi {{ $ticket->user->name }},</p>
        <p>Our support team has replied to your ticket:</p>
        <div class="message-box">
            {{ $message->body }}
        </div>
        <a href="{{ url('/dashboard/support/' . $ticket->id) }}" class="btn">View Full Conversation</a>
        <p style="margin-top: 20px; font-size: 12px; color: #9ca3af;">Ticket #{{ $ticket->id }} · {{ $ticket->subject }}</p>
    </div>
</div>
</body>
</html>
