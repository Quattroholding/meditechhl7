<?php

namespace App\Livewire\User;

use App\Models\User;
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

    protected $queryString = [
        'search' => ['except' => ''],
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


    public function render()
    {
        $data = User::when(auth()->user()->hasRole('admin client') or auth()->user()->hasRole('doctor'),function ($q){
            $q->whereHas('clients',function ($q2){
                $q2->whereIn('user_clients.client_id',auth()->user()->clients()->pluck('client_id'));
            });
        })
        ->whereDoesntHave('practitioner')
        ->whereDoesntHave('patient')
        ->when($this->search, function (Builder $query) {
            $query->where(function ($q) {
                $q->orWhere('first_name', 'like', '%' . $this->search . '%');
                $q->orWhere('last_name', 'like', '%' . $this->search . '%');
                $q->orWhere('email', 'like', '%' . $this->search . '%');
            });
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->pagination);

        return view('livewire.user.data-table', ['data' => $data]);

    }

    public function editUser($userId)
    {
        $this->modalTitle = 'Actualizar Usuario';
        $this->dispatch('editUserModal', $userId);
    }
}
