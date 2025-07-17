<?php

namespace App\Livewire\Permission;

use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

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

    public function deletePermission($permissionId)
    {
        $permission = Permission::findOrFail($permissionId);

        if ($permission->roles()->count() > 0) {
            $this->dispatch('swal:alert', [
                'type' => 'error',
                'title' => 'Error',
                'text' => 'No se puede eliminar el permiso porque está asignado a roles.'
            ]);
            return;
        }

        $permission->delete();

        $this->dispatch('swal:alert', [
            'type' => 'success',
            'title' => 'Éxito',
            'text' => 'Permiso eliminado exitosamente.'
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
        $permissions = Permission::where('name', 'like', '%' . $this->search . '%')
            ->withCount('roles')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.permission.data-table', compact('permissions'));
    }

    public function openPermissionModal($id)
    {
        $this->dispatch();
    }
}
