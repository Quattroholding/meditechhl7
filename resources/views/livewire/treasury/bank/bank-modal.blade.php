<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">
                {{ $this->bank ? 'Editar Banco' : 'Nuevo Banco' }}
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
            {{-- Bank Name --}}
            <div>
                <label class="block text-sm font-medium mb-1">Nombre del Banco *</label>
                <input
                    type="text"
                    wire:model="bank_name"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ej: Banco Latinoamericano"
                />
                @error('bank_name')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Account Number --}}
            <div>
                <label class="block text-sm font-medium mb-1">Número de Cuenta *</label>
                <input
                    type="text"
                    wire:model="account_number"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Ej: 123456789"
                />
                @error('account_number')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Account Type --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de Cuenta *</label>
                    <select
                        wire:model="account_type"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="checking">Cuenta Corriente</option>
                        <option value="savings">Cuenta de Ahorros</option>
                        <option value="money_market">Mercado de Dinero</option>
                        <option value="credit_line">Línea de Crédito</option>
                    </select>
                    @error('account_type')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Currency --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Moneda *</label>
                    <select
                        wire:model="currency"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="PAB">Balboa (PAB)</option>
                        <option value="USD">Dólar (USD)</option>
                        <option value="EUR">Euro (EUR)</option>
                    </select>
                    @error('currency')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
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
                    <option value="active">Activo</option>
                    <option value="inactive">Inactivo</option>
                    <option value="suspended">Suspendido</option>
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
                @if ($this->bank)
                    <button
                        type="button"
                        wire:click="delete"
                        wire:confirm="¿Está seguro de que desea eliminar este banco?"
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
