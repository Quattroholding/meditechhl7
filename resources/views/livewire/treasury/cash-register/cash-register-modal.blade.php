@if($isModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            {{-- Header --}}
            <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold">
                    {{ $this->cashRegister ? 'Editar Caja' : 'Nueva Caja' }}
                </h3>
                <button
                    wire:click="$dispatch('closeModal')"
                    class="text-gray-500 hover:text-gray-700"
                >
                    ✕
                </button>
            </div>

            {{-- Form --}}
            <form wire:submit.prevent="save" class="p-6 space-y-4">
@else
    <form wire:submit.prevent="save" class="space-y-4">
@endif
            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium mb-1">Nombre de la Caja *</label>
                <input
                    type="text"
                    wire:model="name"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ej: Caja Principal"
                />
                @error('name')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Branch --}}
            <div>
                <label class="block text-sm font-medium mb-1">Sucursal *</label>
                <select
                    wire:model="branch_id"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="0">Seleccionar sucursal...</option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_id')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Responsible User --}}
            <div>
                <label class="block text-sm font-medium mb-1">Usuario Responsable *</label>
                <select
                    wire:model="responsible_user_id"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="0">Seleccionar usuario...</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
                @error('responsible_user_id')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Accounting Account --}}
            <div>
                <label class="block text-sm font-medium mb-1">Cuenta Contable *</label>
                <select
                    wire:model="accounting_account_id"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="0">Seleccionar cuenta...</option>
                    @foreach ($accounts as $account)
                        <option value="{{ $account->id }}">
                            {{ $account->code }} - {{ $account->name }}
                        </option>
                    @endforeach
                </select>
                @error('accounting_account_id')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium mb-1">Estado</label>
                <select
                    wire:model="status"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="active">Activa</option>
                    <option value="closed">Cerrada</option>
                    <option value="inactive">Inactiva</option>
                </select>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 pt-4 border-t">
                @if($isModal)
                    <button
                        type="button"
                        wire:click="$dispatch('closeModal')"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50"
                    >
                        Cancelar
                    </button>
                @endif
                @if ($this->cashRegister)
                    <button
                        type="button"
                        wire:click="delete"
                        wire:confirm="¿Está seguro de que desea eliminar esta caja?"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Eliminar
                    </button>
                @endif
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Guardar
                </button>
            </div>
            </form>
        @if($isModal)
            </div>
        </div>
        @endif
