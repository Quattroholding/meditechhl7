<?php

namespace App\Livewire\Role;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class DataTable extends Component
{
    use WithPagination;

    public $sortDirection='asc';
    public $sortField='name';
    public $search = '';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteRole($roleId)
    {
        $role = Role::findOrFail($roleId);

        if ($role->users()->count() > 0) {
            $this->dispatch('swal:alert', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se puede eliminar el rol porque está asignado a usuarios.'
            ]);
            return;
        }

        $role->delete();

        $this->dispatch('swal:alert', [
            'type' => 'success',
            'title' => 'Éxito',
            'text' => 'Rol eliminado exitosamente.'
        ]);
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $roles = Role::where('name', 'like', '%' . $this->search . '%')
            ->withCount('users')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.role.data-table', compact('roles'));
    }
}
