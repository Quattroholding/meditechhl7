@if($isModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            {{-- Header --}}
            <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold">
                    {{ $this->supplier ? 'Editar Proveedor' : 'Nuevo Proveedor' }}
                </h3>
                <button
                    wire:click="$emit('closeModal')"
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
                {{-- RUC --}}
                <div>
                    <label class="block text-sm font-medium mb-1">RUC *</label>
                    <input
                        type="text"
                        wire:model="ruc"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: 123456789"
                    />
                    @error('ruc')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Credit Days --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Días de Crédito *</label>
                    <input
                        type="number"
                        wire:model="credit_days"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        min="0"
                        max="365"
                    />
                    @error('credit_days')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Razón Social *</label>
                <input
                    type="text"
                    wire:model="legal_name"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ej: Empresa XYZ S.A."
                />
                @error('legal_name')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nombre Comercial</label>
                <input
                    type="text"
                    wire:model="commercial_name"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ej: XYZ"
                />
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Correo Electrónico</label>
                    <input
                        type="email"
                        wire:model="email"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Teléfono</label>
                    <input
                        type="tel"
                        wire:model="phone"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Persona de Contacto</label>
                <input
                    type="text"
                    wire:model="contact_person"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                />
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Dirección</label>
                <textarea
                    wire:model="address"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="2"
                ></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Cuenta Contable CxP *</label>
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

            <div>
                <label class="block text-sm font-medium mb-1">Estado</label>
                <select
                    wire:model="status"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
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
                @if ($this->supplier)
                    <button
                        type="button"
                        wire:click="delete"
                        wire:confirm="¿Está seguro de que desea eliminar este proveedor?"
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
