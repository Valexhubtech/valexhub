<?php

namespace App\Http\Controllers;

use App\Mail\SupportTicketCreatedMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Wave\SupportMessage;
use Wave\SupportTicket;

class SupportTicketController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string|max:5000',
            'deployment_id' => 'nullable|integer',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => auth()->id(),
            'deployment_id' => $validated['deployment_id'] ?? null,
            'subject' => $validated['subject'],
            'status' => 'open',
            'priority' => 'medium',
        ]);

        SupportMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'body' => $validated['body'],
            'is_admin' => false,
        ]);

        try {
            Mail::to(config('mail.from.address'))
                ->queue(new SupportTicketCreatedMail($ticket, $validated['body']));
        } catch (\Throwable $e) {
            Log::error('SupportTicket notification mail failed', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('dashboard.support.show', ['supportTicket' => $ticket->id])
            ->with('status', 'Your support ticket has been submitted. We\'ll get back to you shortly.');
    }
}
