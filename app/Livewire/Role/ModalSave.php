<?php

namespace App\Livewire\Role;

use Livewire\Attributes\Modelable;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class ModalSave extends Component
{
    public $role;
    public $name = '';
    public $selectedPermissions = [];
    public $isEdit = false;
    public $showModal=false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'selectedPermissions' => 'array'
    ];

    protected $messages = [
        'name.required' => 'El nombre del rol es requerido.',
        'name.string' => 'El nombre debe ser una cadena de texto.',
        'name.max' => 'El nombre no puede tener más de 255 caracteres.',
    ];

    public function mount($roleId = null)
    {
        $this->resetForm();
        if ($roleId) {
            $this->loadRole($roleId);
        }
    }

    #[On('open-role-modal')]
    public function openModal($roleId = null)
    {
        $this->resetForm();
        if ($roleId) {
            $this->loadRole($roleId);
            $this->showModal=true;
        }
    }

    #[On('close-modal')]
    public function closeModal()
    {
        $this->resetForm();
        $this->showModal=false;
    }

    private function loadRole($roleId)
    {
        $this->role = Role::with('permissions')->findOrFail($roleId);
        $this->name = $this->role->name;
        $this->selectedPermissions = $this->role->permissions->pluck('id')->toArray();
        $this->isEdit = true;
    }

    private function resetForm()
    {
        $this->role = null;
        $this->name = '';
        $this->selectedPermissions = [];
        $this->isEdit = false;
        $this->resetErrorBag();
    }

    public function save()
    {
        $this->validate();

        // Check for unique name
        if ($this->isEdit) {
            $this->validate([
                'name' => 'required|string|max:255|unique:roles,name,' . $this->role->id,
            ]);
        } else {
            $this->validate([
                'name' => 'required|string|max:255|unique:roles,name',
            ]);
        }

        DB::transaction(function () {
            if ($this->isEdit) {
                $this->role->update(['name' => $this->name]);
                $role = $this->role;
            } else {
                $role = Role::create(['name' => $this->name]);
            }

            $role->syncPermissions($this->selectedPermissions);
        });

        $this->dispatch('swal:alert', [
            'type' => 'success',
            'title' => 'Éxito',
            'text' => $this->isEdit ? 'Rol actualizado exitosamente.' : 'Rol creado exitosamente.'
        ]);

        $this->dispatch('role-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $permissionsByModule = $permissions->groupBy('module');

        return view('livewire.role.modal-save', compact('permissionsByModule'));
    }
}
