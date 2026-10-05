<div class="space-y-4">
    {{-- Header --}}
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Proveedores</h2>
        <button
            wire:click="openModal"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
            + Nuevo Proveedor
        </button>
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <input
                type="text"
                wire:model.live="search"
                placeholder="Buscar por RUC o nombre..."
                class="w-full px-4 py-2 border rounded-lg"
            />
        </div>
        <div>
            <select wire:model.live="status" class="w-full px-4 py-2 border rounded-lg">
                <option value="">Todos los estados</option>
                <option value="active">Activo</option>
                <option value="inactive">Inactivo</option>
            </select>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="w-full">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">RUC</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Razón Social</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Contacto</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Teléfono</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">Estado</th>
                    <th class="px-6 py-3 text-right text-sm font-semibold">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($suppliers as $supplier)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">{{ $supplier->ruc }}</td>
                        <td class="px-6 py-4 text-sm font-medium">{{ $supplier->legal_name }}</td>
                        <td class="px-6 py-4 text-sm">{{ $supplier->contact_person ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">{{ $supplier->phone ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium {{ $supplier->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}"
                            >
                                {{ $supplier->status === 'active' ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <button
                                wire:click="openModal({{ $supplier->id }})"
                                class="text-blue-600 hover:text-blue-900"
                            >
                                Editar
                            </button>
                            <button
                                wire:click="toggleStatus({{ $supplier->id }})"
                                class="text-amber-600 hover:text-amber-900"
                            >
                                {{ $supplier->status === 'active' ? 'Desactivar' : 'Activar' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No hay proveedores registrados
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div class="flex justify-center">
        {{ $suppliers->links() }}
    </div>

    {{-- Modal --}}
    @if ($showModal)
        @livewire('finance.supplier.supplier-modal', ['supplier' => $editingSupplier], key('supplier-modal-' . ($editingSupplier?->id ?? 'new')))
    @endif
</div>
