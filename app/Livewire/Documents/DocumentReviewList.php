<?php

namespace App\Livewire\Documents;

use App\Models\DocumentUpload;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentReviewList extends Component
{
    use WithPagination;

    #[Reactive]
    public string $statusFilter = 'all';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    public string $search = '';

    public ?int $selectedDocumentId = null;

    protected $paginationTheme = 'bootstrap';

    public function getStatusOptions(): array
    {
        return [
            'all' => 'Todos',
            'pending_parsing' => 'Pendiente de Procesamiento',
            'parsing' => 'Procesando',
            'parsing_failed' => 'Error en Procesamiento',
            'parsed' => 'Listo para Revisar',
            'approved' => 'Aprobado',
            'processing' => 'Procesando Inventario',
            'processed' => 'Completado',
            'rejected' => 'Rechazado',
        ];
    }

    public function updatedStatusFilter(): void
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
        $this->redirect(route('documents.detail', $documentId));
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
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
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
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }
}
