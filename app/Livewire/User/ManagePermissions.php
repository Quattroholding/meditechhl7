<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

class ManagePermissions extends Component
{
    public $userId;

    public $user;

    public $showModal = false;

    public $selectedPermissions = [];

    public $allPermissions = [];

    public $rolePermissions = [];

    public $directPermissions = [];

    public $search = '';

    protected $listeners = ['open-manage-permissions-modal' => 'openModal'];

    public function mount($userId = null)
    {
        if ($userId) {
            $this->userId = $userId;
            $this->loadUser();
        }
    }

    #[On('open-manage-permissions-modal')]
    public function openModal($userId)
    {
        $this->userId = $userId;
        $this->loadUser();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['search', 'userId', 'user', 'selectedPermissions', 'allPermissions', 'rolePermissions', 'directPermissions']);
    }

    public function updatedSelectedPermissions($value)
    {
        // Limpiar y validar los permisos seleccionados
        $this->selectedPermissions = array_values(
            array_filter(
                array_map('intval', $this->selectedPermissions),
                fn ($id) => $id > 0
            )
        );
    }

    private function loadUser()
    {
        $this->user = User::with(['roles.permissions', 'permissions'])->findOrFail($this->userId);

        // Obtener todos los permisos
        $this->allPermissions = Permission::orderBy('name')->get();

        // Permisos que vienen de los roles del usuario
        $this->rolePermissions = $this->user->getPermissionsViaRoles()->pluck('id')->toArray();

        // Permisos directos del usuario (no heredados de roles)
        $this->directPermissions = $this->user->permissions()->pluck('permissions.id')->toArray();

        // Obtener todos los IDs válidos de permisos para validación
        $validPermissionIds = $this->allPermissions->pluck('id')->toArray();

        // Permisos seleccionados (directos del usuario), filtrando solo IDs válidos
        $this->selectedPermissions = array_values(array_intersect($this->directPermissions, $validPermissionIds));
    }

    public function save()
    {
        $this->authorize('manage-permissions');

        try {
            // Convertir a enteros y filtrar solo IDs válidos de permisos
            $selectedIds = array_map('intval', array_filter($this->selectedPermissions));

            // Obtener los permisos válidos
            $validPermissions = Permission::whereIn('id', $selectedIds)->pluck('name')->toArray();

            // Sincronizar permisos directos del usuario usando los nombres de permiso
            // Solo sincronizamos los permisos directos, los de roles se mantienen
            $this->user->syncPermissions($validPermissions);

            // Refrescar el usuario para obtener datos actualizados
            $this->user->refresh();

            $this->dispatch('showToastrManagePermissions',
                type: 'success',
                message: 'Permisos del usuario actualizados exitosamente.',
            );

            $this->closeModal();
            $this->dispatch('permissions-updated');
        } catch (\Exception $e) {
            \Log::error('Error al actualizar permisos del usuario', [
                'user_id' => $this->userId,
                'selected_permissions' => $this->selectedPermissions,
                'error' => $e->getMessage(),
            ]);

            $this->dispatch('showToastrManagePermissions',
                type: 'error',
                message: 'Error al actualizar los permisos: '.$e->getMessage(),
            );
        }
    }

    public function getFilteredPermissionsProperty()
    {
        // Asegurar que siempre tengamos una Collection
        $allPermissions = is_array($this->allPermissions)
            ? collect($this->allPermissions)
            : $this->allPermissions;

        if (empty($this->search)) {
            return $allPermissions;
        }

        return $allPermissions->filter(function ($permission) {
            return str_contains(strtolower($permission->name), strtolower($this->search));
        });
    }

    public function getGroupedPermissionsProperty()
    {
        $permissions = $this->filteredPermissions;

        // Asegurar que es una Collection
        if (is_array($permissions)) {
            $permissions = collect($permissions);
        }

        // Agrupar permisos por módulo (la parte antes del primer punto o guión)
        $grouped = $permissions->groupBy(function ($permission) {
            // Extraer el módulo del nombre del permiso
            $parts = preg_split('/[.\-_]/', $permission->name);

            return $parts[0] ?? 'otros';
        });

        // Ordenar los grupos alfabéticamente
        return $grouped->sortKeys();
    }

    public function getModuleDisplayNameProperty()
    {
        return function ($moduleName) {
            // Convertir nombres de módulos a formato amigable
            $displayNames = [
                'users' => 'Usuarios',
                'patients' => 'Pacientes',
                'appointments' => 'Citas',
                'practitioners' => 'Médicos',
                'consultations' => 'Consultas',
                'branches' => 'Sucursales',
                'clients' => 'Clientes',
                'rooms' => 'Consultorios',
                'medicines' => 'Medicamentos',
                'medication' => 'Medicación',
                'invoices' => 'Facturas',
                'payments' => 'Pagos',
                'service' => 'Servicios',
                'inventory' => 'Inventario',
                'reports' => 'Reportes',
                'settings' => 'Configuración',
                'dashboard' => 'Dashboard',
                'manage' => 'Gestión',
                'tickets' => 'Tickets',
                'surveys' => 'Encuestas',
                'quotations' => 'Cotizaciones',
                'suscriptions' => 'Suscripciones',
                'view' => 'Visualización',
                'export' => 'Exportación',
                'algorithms' => 'Algoritmos',
                'otros' => 'Otros',
            ];

            return $displayNames[$moduleName] ?? ucfirst($moduleName);
        };
    }

    public function isPermissionFromRole($permissionId)
    {
        return in_array($permissionId, $this->rolePermissions);
    }

    public function isDirectPermission($permissionId)
    {
        return in_array($permissionId, $this->directPermissions);
    }

    public function render()
    {
        return view('livewire.user.manage-permissions', [
            'groupedPermissions' => $this->groupedPermissions,
            'moduleDisplayName' => $this->moduleDisplayName,
        ]);
    }
}
