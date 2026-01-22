<?php

namespace App\Livewire\TicketSystem;

use App\Models\Ticket;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    // Búsqueda
    public $search = '';

    // Filtros
    public $filterStatus = '';

    public $filterPriority = '';

    public $filterCategory = '';

    public $filterAssigned = ''; // 'me', 'unassigned', 'all'

    // Ordenamiento
    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    // Paginación
    public $pagination = 10;

    // Modal
    public $showDetailModal = false;

    public $selectedTicketId = null;

    /**
     * Resetear paginación cuando cambian los filtros
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterPriority()
    {
        $this->resetPage();
    }

    public function updatedFilterCategory()
    {
        $this->resetPage();
    }

    public function updatedFilterAssigned()
    {
        $this->resetPage();
    }

    /**
     * Ordenamiento
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Abrir modal de detalles
     */
    public function openDetailModal($ticketId)
    {
        $this->selectedTicketId = $ticketId;
        $this->dispatch('openTicketDetailModal', ticketId: $ticketId);
    }

    /**
     * Listener para refrescar tabla
     */
    #[On('refreshTable')]
    public function refreshTable()
    {
        // Solo refrescar
    }

    /**
     * Renderizar vista
     */
    public function render()
    {
        // Construir query
        $query = Ticket::query()
            ->with(['user', 'assignedTo', 'comments']);

        // Búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('identifier', 'like', '%'.$this->search.'%')
                    ->orWhere('title', 'like', '%'.$this->search.'%')
                    ->orWhere('description', 'like', '%'.$this->search.'%');
            });
        }

        // Filtros
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }

        if ($this->filterCategory) {
            $query->where('category', $this->filterCategory);
        }

        if ($this->filterAssigned === 'me') {
            $query->where('assigned_to', auth()->id());
        } elseif ($this->filterAssigned === 'unassigned') {
            $query->whereNull('assigned_to');
        }

        // Ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);

        // Paginación
        $tickets = $query->paginate($this->pagination);

        // Estadísticas
        $stats = [
            'open' => Ticket::open()->count(),
            'in_progress' => Ticket::inProgress()->count(),
            'resolved' => Ticket::resolved()->count(),
            'my_tickets' => Ticket::where('user_id', auth()->id())->count(),
        ];

        return view('livewire.ticket-system.data-table', [
            'tickets' => $tickets,
            'stats' => $stats,
        ]);
    }
}
