<?php

namespace App\Livewire\Permission;

use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class ModalSave extends Component
{
    public $permission;

    public $name = '';

    public $description = '';

    public $isEdit = false;

    public $showModal = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
    ];

    protected $messages = [
        'name.required' => 'El nombre del permiso es requerido.',
        'name.string' => 'El nombre debe ser una cadena de texto.',
        'name.max' => 'El nombre no puede tener más de 255 caracteres.',
        'description.max' => 'La descripción no puede tener más de 500 caracteres.',
    ];

    public function mount($permissionId = null)
    {
        $this->resetForm();
        if ($permissionId) {
            $this->loadPermission($permissionId);
        }
    }

    #[On('open-permission-modal')]
    public function openModal($permissionId = null)
    {
        $this->resetForm();
        if ($permissionId) {
            $this->loadPermission($permissionId);
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->resetForm();
        $this->showModal = false;
    }

    private function loadPermission($permissionId)
    {
        $this->permission = Permission::findOrFail($permissionId);
        $this->name = $this->permission->name;
        $this->description = $this->permission->description ?? '';
        $this->isEdit = true;
    }

    private function resetForm()
    {
        $this->permission = null;
        $this->name = '';
        $this->description = '';
        $this->isEdit = false;
        $this->resetErrorBag();

    }

    public function save()
    {
        $this->validate();

        // Check for unique name
        if ($this->isEdit) {
            $this->validate([
                'name' => 'required|string|max:255|unique:permissions,name,'.$this->permission->id,
            ]);
        } else {
            $this->validate([
                'name' => 'required|string|max:255|unique:permissions,name',
            ]);
        }

        if ($this->isEdit) {
            $this->permission->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
        } else {
            Permission::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);
        }

        $this->dispatch('swal:alert', [
            'type' => 'success',
            'title' => 'Éxito',
            'text' => $this->isEdit ? 'Permiso actualizado exitosamente.' : 'Permiso creado exitosamente.',
        ]);

        $this->dispatch('permission-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.permission.modal-save');
    }
}
