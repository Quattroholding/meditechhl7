@if($isModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            {{-- Header --}}
            <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold">
                    {{ $this->account ? 'Editar Cuenta Contable' : 'Nueva Cuenta Contable' }}
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
            <div class="grid grid-cols-2 gap-4">
                {{-- Code --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Código *</label>
                    <input
                        type="text"
                        wire:model="code"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: 1000"
                    />
                    @error('code')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Nombre *</label>
                    <input
                        type="text"
                        wire:model="name"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: Caja"
                    />
                    @error('name')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium mb-1">Descripción</label>
                <textarea
                    wire:model="description"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="2"
                    placeholder="Descripción opcional..."
                ></textarea>
            </div>

            <div class="grid grid-cols-3 gap-4">
                {{-- Account Type --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de Cuenta *</label>
                    <select
                        wire:model="account_type"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Seleccionar tipo...</option>
                        <option value="asset">Activo</option>
                        <option value="liability">Pasivo</option>
                        <option value="equity">Patrimonio</option>
                        <option value="income">Ingresos</option>
                        <option value="expense">Gastos</option>
                        <option value="cost">Costos</option>
                    </select>
                    @error('account_type')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Parent Account --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Cuenta Padre</label>
                    <select
                        wire:model="parent_id"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Sin padre (Raíz)</option>
                        @foreach ($parentAccounts as $acc)
                            <option value="{{ $acc->id }}">
                                {{ $acc->code }} - {{ $acc->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Level --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Nivel</label>
                    <input
                        type="number"
                        wire:model="level"
                        disabled
                        class="w-full px-3 py-2 border rounded-lg bg-gray-100 text-gray-600"
                    />
                </div>
            </div>

            {{-- Allows Transaction --}}
            <div class="flex items-center">
                <input
                    type="checkbox"
                    wire:model="allows_transaction"
                    id="allows_transaction"
                    class="rounded"
                />
                <label for="allows_transaction" class="ml-2 text-sm font-medium">
                    Permite Transacciones
                </label>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium mb-1">Estado</label>
                <select
                    wire:model="status"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="active">Activa</option>
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
                @if ($this->account)
                    <button
                        type="button"
                        wire:click="delete"
                        wire:confirm="¿Está seguro de que desea eliminar esta cuenta?"
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
