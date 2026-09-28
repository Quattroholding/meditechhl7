<?php

namespace App\Livewire\Documents;

use App\Models\DocumentUpload;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentReviewList extends Component
{
    use WithPagination;

    public string $filter = 'parsed';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public string $search = '';

    public ?int $selectedDocumentId = null;

    protected $paginationTheme = 'bootstrap';

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setSortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function selectDocument(int $documentId): void
    {
        $this->selectedDocumentId = $documentId;
        $this->dispatch('openApprovalModal', $documentId);
    }

    #[On('documentUploaded')]
    public function onDocumentUploaded(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = DocumentUpload::query();

        // Apply filter
        if ($this->filter !== 'all') {
            $query->where('status', $this->filter);
        }

        // Apply search
        if ($this->search) {
            $query->where('original_filename', 'like', "%{$this->search}%");
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Paginate
        $documents = $query->paginate(25);

        return view('livewire.documents.document-review-list', [
            'documents' => $documents,
        ]);
    }
}
