<?php

namespace App\Livewire\User;

use App\Models\Client;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class DataTable extends Component
{
    use WithPagination;

    public $search = '';

    public $sortField = 'id';

    public $sortDirection = 'desc';

    public $pagination = 10;

    public $show_create = true;

    public $showModal = false;

    public $modalTitle = 'Usuario';

    public $statusFilter = 'active'; // all, active, inactive

    public $clientFilter = 'all'; // all, or client_id

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'clientFilter' => ['except' => 'all'],
    ];

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

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingClientFilter()
    {
        $this->resetPage();
    }

    /*public function activateUser($userId)
     {
         try {
             $user = User::findOrFail($userId);

             $user->active = true;
             $user->save();
             // Mensaje de éxito
             session()->flash('message.success', 'Usuario activado exitosamente');
         } catch (\Exception $e) {
             session()->flash('message.error', 'Error al activar el usuario');
         }
     }*/

    public function render(): View
    {
        $data = User::when(auth()->user()->hasRole('admin client') or auth()->user()->hasRole('doctor') or auth()->user()->hasRole('asistente medico'), function ($q) {
            $q->whereHas('clients', function ($q2) {
                $q2->whereIn('user_clients.client_id', auth()->user()->clients()->pluck('client_id'));
            });
        })
        // ->whereDoesntHave('practitioner')
            ->when(! auth()->user()->hasRole('admin'), function (Builder $query) {
                $query->whereDoesntHave('patient');
            })
            ->when($this->search, function (Builder $query) {
                $query->where(function ($q) {
                    $q->orWhere('first_name', 'like', '%'.$this->search.'%');
                    $q->orWhere('last_name', 'like', '%'.$this->search.'%');
                    $q->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', function (Builder $query) {
                $query->where('active', $this->statusFilter === 'active' ? 1 : 0);
            })
            ->when($this->clientFilter !== 'all', function (Builder $query) {
                $query->whereHas('clients', function ($q) {
                    $q->where('user_clients.client_id', $this->clientFilter);
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->pagination);

        $clients = ['all' => 'Todos'] + Client::select('id', 'name')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        return view('livewire.user.data-table', [
            'data' => $data,
            'clients' => $clients,
        ]);

    }

    public function editUser($userId)
    {
        $this->modalTitle = 'Actualizar Usuario';
        $this->dispatch('editUserModal', $userId);
    }

    public function openPermissionsModal($userId)
    {
        $this->dispatch('open-manage-permissions-modal', userId: $userId);
    }
}
