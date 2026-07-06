<?php

namespace App\Livewire;

use App\Mail\SupportReplyMail;
use App\Mail\SupportTicketCreatedMail;
use App\Models\User;
use Livewire\Component;
use Wave\SupportMessage;
use Wave\SupportTicket;

class SupportThread extends Component
{
    public SupportTicket $ticket;
    public string $reply = '';

    protected function user(): User
    {
        /** @var User $user */
        $user = auth()->user();
        return $user;
    }

    public function mount(SupportTicket $ticket): void
    {
        abort_unless($ticket->user_id === $this->user()->id, 403);
        $this->ticket = $ticket;
    }

    public function sendReply(): void
    {
        $this->validate(['reply' => 'required|string|max:5000']);

        abort_unless($this->ticket->user_id === $this->user()->id, 403);
        abort_unless($this->ticket->isOpen(), 422);

        SupportMessage::create([
            'ticket_id' => $this->ticket->id,
            'user_id'   => $this->user()->id,
            'body'      => $this->reply,
            'is_admin'  => false,
        ]);

        if ($this->ticket->status === 'open') {
            $this->ticket->update(['status' => 'in_progress']);
        }

        // Notify admin of client reply
        \Illuminate\Support\Facades\Mail::to(config('mail.from.address'))
            ->queue(new SupportTicketCreatedMail($this->ticket, $this->reply));

        $this->reply = '';
        $this->ticket->refresh();
    }

    public function render()
    {
        return view('livewire.support-thread', [
            'messages' => $this->ticket->messages()->with('user')->oldest()->get(),
        ]);
    }
}
