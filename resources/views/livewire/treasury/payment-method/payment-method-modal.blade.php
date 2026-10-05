<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">
                {{ $this->paymentMethod ? 'Editar Método de Pago' : 'Nuevo Método de Pago' }}
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
            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium mb-1">Nombre del Método *</label>
                <input
                    type="text"
                    wire:model="name"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ej: Transferencia Bancaria"
                />
                @error('name')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Destination Type --}}
            <div>
                <label class="block text-sm font-medium mb-1">Tipo de Destino *</label>
                <select
                    wire:model="destination_type"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="bank">Banco</option>
                    <option value="cash">Caja</option>
                </select>
                @error('destination_type')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Bank Selection (conditional) --}}
            @if ($destination_type === 'bank')
                <div>
                    <label class="block text-sm font-medium mb-1">Banco por Defecto *</label>
                    <select
                        wire:model="default_bank_id"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Seleccionar banco...</option>
                        @foreach ($banks as $bank)
                            <option value="{{ $bank->id }}">
                                {{ $bank->bank_name }} - {{ $bank->account_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('default_bank_id')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            {{-- Cash Register Selection (conditional) --}}
            @if ($destination_type === 'cash')
                <div>
                    <label class="block text-sm font-medium mb-1">Caja por Defecto *</label>
                    <select
                        wire:model="default_cash_register_id"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Seleccionar caja...</option>
                        @foreach ($cashRegisters as $register)
                            <option value="{{ $register->id }}">
                                {{ $register->name }} ({{ $register->branch->name }})
                            </option>
                        @endforeach
                    </select>
                    @error('default_cash_register_id')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            @endif

            {{-- Status --}}
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
                <button
                    type="button"
                    wire:click="$dispatch('closeModal')"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-50"
                >
                    Cancelar
                </button>
                @if ($this->paymentMethod)
                    <button
                        type="button"
                        wire:click="delete"
                        wire:confirm="¿Está seguro de que desea eliminar este método de pago?"
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
    </div>
</div>
