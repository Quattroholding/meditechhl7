<?php

namespace App\Livewire\Email;

use App\Services\MicrosoftGraphService;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;
use Livewire\WithPagination;

class OutboxDataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'sentDateTime';

    public $sortDirection = 'desc';

    public $perPage = 25;

    public $selectedEmail = null;

    public $showDetailModal = false;

    protected $listeners = ['refreshEmails' => '$refresh'];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
        $this->resetPage();
    }

    public function viewDetails($messageId)
    {
        try {
            $graphService = new MicrosoftGraphService;
            $this->selectedEmail = $graphService->getEmailDetails($messageId);
            $this->showDetailModal = true;
        } catch (Exception $e) {
            session()->flash('error', 'Error al obtener detalles del correo: '.$e->getMessage());
        }
    }

    public function closeDetailModal()
    {
        $this->showDetailModal = false;
        $this->selectedEmail = null;
    }

    public function render()
    {
        try {
            $graphService = new MicrosoftGraphService;

            // Calcular el skip para paginación
            $currentPage = $this->getPage();
            $skip = ($currentPage - 1) * $this->perPage;

            // Obtener correos
            if ($this->search) {
                $result = $graphService->searchSentEmails($this->search, $this->perPage);
                $emails = $result['messages'];
                $totalCount = $result['total_count'];
            } else {
                $result = $graphService->getSentEmails($this->perPage, $skip);
                $emails = $result['messages'];
                $totalCount = $result['total_count'];
            }

            // Crear una colección para manejar la paginación manualmente
            $paginatedEmails = new LengthAwarePaginator(
                $emails,
                $totalCount,
                $this->perPage,
                $currentPage,
                ['path' => request()->url(), 'query' => request()->query()]
            );

            return view('livewire.email.outbox-data-table', [
                'emails' => $paginatedEmails,
                'isConnected' => $result['success'] ?? false,
                'errorMessage' => $result['error'] ?? null,
            ]);
        } catch (Exception $e) {
            return view('livewire.email.outbox-data-table', [
                'emails' => new LengthAwarePaginator([], 0, $this->perPage, 1),
                'isConnected' => false,
                'errorMessage' => $e->getMessage(),
            ]);
        }
    }
}
