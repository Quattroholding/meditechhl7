<?php

namespace App\Livewire\Documents;

use App\Models\DocumentUpload;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentReviewList extends Component
{
    use WithPagination;

    public string $search = '';

    public string $sortField = 'created_at';

    public string $sortDirection = 'desc';

    public int $pagination = 25;

    public string $statusFilter = 'all';

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

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

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    #[On('documentUploaded')]
    public function onDocumentUploaded(): void
    {
        $this->resetPage();
    }

    public function getDataProperty()
    {
        $query = DocumentUpload::query();

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply search
        if ($this->search) {
            $query->where('original_filename', 'like', "%{$this->search}%");
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->pagination);
    }

    public function render()
    {
        return view('livewire.documents.document-review-list', [
            'documents' => $this->data,
            'statusOptions' => $this->getStatusOptions(),
        ]);
    }
}
