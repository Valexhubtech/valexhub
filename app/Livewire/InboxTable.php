<?php

namespace App\Livewire;

use App\Services\ResendEmailService;
use Livewire\Component;
use Livewire\WithPagination;

class InboxTable extends Component
{
    use WithPagination;

    public $search = '';

    public $status = '';

    public $selectedEmail = null;

    public $showModal = false;

    public $emailEvents = [];

    public $showEvents = false;

    public $configuredDomain = '';

    protected $resendService;

    public function mount()
    {
        $this->resendService = new ResendEmailService();

        // Get the configured domain from environment
        $this->configuredDomain = $this->resendService->getConfiguredDomain() ?? 'Not configured';
    }

    protected function getResendService()
    {
        if (! $this->resendService) {
            $this->resendService = new ResendEmailService();
        }

        return $this->resendService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function viewEmail($emailId)
    {
        $resendService = $this->getResendService();
        $emails = $resendService->getSentEmails(100);
        $formatted = $resendService->formatEmailsForDisplay($emails);

        $this->selectedEmail = $formatted->firstWhere('id', $emailId);

        if ($this->selectedEmail) {
            // Get full email details
            $emailDetails = $resendService->getEmail($emailId);
            if ($emailDetails) {
                // Format the email details to ensure arrays are converted to strings
                $formattedDetails = [
                    'html' => $emailDetails['html'] ?? null,
                    'text' => $emailDetails['text'] ?? null,
                    'to' => is_array($emailDetails['to'] ?? []) ? implode(', ', $emailDetails['to']) : ($emailDetails['to'] ?? ''),
                    'from' => $emailDetails['from'] ?? '',
                    'subject' => $emailDetails['subject'] ?? '',
                    'bcc' => is_array($emailDetails['bcc'] ?? []) ? implode(', ', $emailDetails['bcc']) : ($emailDetails['bcc'] ?? ''),
                    'cc' => is_array($emailDetails['cc'] ?? []) ? implode(', ', $emailDetails['cc']) : ($emailDetails['cc'] ?? ''),
                    'reply_to' => is_array($emailDetails['reply_to'] ?? []) ? implode(', ', $emailDetails['reply_to']) : ($emailDetails['reply_to'] ?? ''),
                ];

                $this->selectedEmail = array_merge($this->selectedEmail, $formattedDetails);
            }
        }

        $this->showModal = true;
    }

    public function viewEvents($emailId)
    {
        $resendService = $this->getResendService();
        $this->emailEvents = $resendService->getEmailEvents($emailId);
        $this->showEvents = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedEmail = null;
    }

    public function closeEvents()
    {
        $this->showEvents = false;
        $this->emailEvents = [];
    }

    public function refresh()
    {
        // Force refresh by clearing any cached data
        $this->resetPage();
    }

    public function testApiConnection()
    {
        $resendService = $this->getResendService();
        $result = $resendService->testConnection();

        if ($result['success']) {
            session()->flash('api_test', 'API connection successful! Found '.count($result['data']['data'] ?? []).' domains.');
        } else {
            session()->flash('api_error', 'API connection failed: '.$result['error']);
        }
    }

    public function debugEmails()
    {
        $resendService = $this->getResendService();
        $emails = $resendService->getSentEmails(10); // Get just 10 for debugging
        $formatted = $resendService->formatEmailsForDisplay($emails);

        $domains = $formatted->pluck('from')->unique()->values();
        $configuredDomain = $resendService->getConfiguredDomain();

        session()->flash(
            'debug_info',
            'Configured Domain: '.$configuredDomain.
            ' | Found Domains: '.$domains->implode(', ').
            ' | Total Emails: '.$formatted->count()
        );
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->resetPage();
    }

    public function getEmailsProperty()
    {
        $resendService = $this->getResendService();
        $emails = $resendService->getSentEmails(100);
        $formatted = $resendService->formatEmailsForDisplay($emails);

        // Apply filters
        if ($this->search) {
            $formatted = $formatted->filter(function ($email) {
                return str_contains(strtolower($email['from']), strtolower($this->search)) ||
                       str_contains(strtolower($email['to']), strtolower($this->search)) ||
                       str_contains(strtolower($email['subject']), strtolower($this->search));
            });
        }

        if ($this->status) {
            $formatted = $formatted->where('status', $this->status);
        }

        return $formatted->sortByDesc('created_at');
    }

    public function render()
    {
        $emails = $this->emails;
        $paginatedEmails = $emails->forPage($this->getPage(), 25);

        return view('livewire.inbox-table', [
            'emails' => $paginatedEmails,
            'totalEmails' => $emails->count(),
        ]);
    }
}
