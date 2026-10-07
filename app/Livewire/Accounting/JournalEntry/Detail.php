<?php

namespace App\Livewire\Accounting\JournalEntry;

use App\Models\Accounting\JournalEntry;
use Livewire\Component;

class Detail extends Component
{
    public JournalEntry $journalEntry;

    public function mount(JournalEntry $journalEntry)
    {
        $this->journalEntry = $journalEntry->load(['journalEntryLines.accountingAccount', 'accountingPeriod', 'createdBy', 'postedBy']);
    }

    public function reverse()
    {
        $this->authorize('accounting.entries.reverse');

        try {
            $this->journalEntry->reverse(auth()->user());
            $this->dispatch('showToastr', type: 'success', message: 'Asiento revertido correctamente');
            $this->redirect(route('accounting.journal-entries'));
        } catch (\Exception $e) {
            $this->dispatch('showToastr', type: 'error', message: 'Error al revertir: '.$e->getMessage());
        }
    }

    public function post()
    {
        $this->authorize('accounting.entries.post');

        try {
            $this->journalEntry->post(auth()->user());
            $this->dispatch('showToastr', type: 'success', message: 'Asiento contabilizado correctamente');
            $this->journalEntry->refresh();
        } catch (\Exception $e) {
            $this->dispatch('showToastr', type: 'error', message: 'Error al contabilizar: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.accounting.journal-entry.detail');
    }
}
