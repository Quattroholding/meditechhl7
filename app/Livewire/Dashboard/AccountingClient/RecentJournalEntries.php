<?php

namespace App\Livewire\Dashboard\AccountingClient;

use App\Models\Accounting\JournalEntry;
use Livewire\Component;

class RecentJournalEntries extends Component
{
    public array $entries = [];

    public int $totalPosted = 0;

    public int $totalDraft = 0;

    public function mount(): void
    {
        $this->loadRecentEntries();
    }

    public function loadRecentEntries(): void
    {
        $clientId = auth()->user()->getCurrentClient()->id;

        // Últimos 5 asientos
        $this->entries = JournalEntry::where('client_id', $clientId)
            ->latest('entry_date')
            ->take(5)
            ->get()
            ->map(function ($entry) {
                return [
                    'number' => $entry->entry_number,
                    'description' => $entry->description,
                    'date' => $entry->entry_date->format('d/m/Y'),
                    'status' => $entry->status,
                    'total' => $entry->journalEntryLines->sum('debit'),
                ];
            })
            ->toArray();

        // Contar asientos por estado
        $this->totalPosted = JournalEntry::where('client_id', $clientId)
            ->where('status', 'posted')
            ->count();

        $this->totalDraft = JournalEntry::where('client_id', $clientId)
            ->where('status', 'draft')
            ->count();
    }

    public function render()
    {
        return view('livewire.dashboard.accounting-client.recent-journal-entries');
    }
}
