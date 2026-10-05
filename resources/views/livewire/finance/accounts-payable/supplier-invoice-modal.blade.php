<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">
                {{ $this->invoice ? 'Editar Factura' : 'Nueva Factura de Proveedor' }}
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
            {{-- Supplier --}}
            <div>
                <label class="block text-sm font-medium mb-1">Proveedor *</label>
                <select
                    wire:model="supplier_id"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="0">Seleccionar proveedor...</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->legal_name }} ({{ $supplier->ruc }})
                        </option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            <div class="grid grid-cols-3 gap-4">
                {{-- Invoice Number --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Número Factura *</label>
                    <input
                        type="text"
                        wire:model="invoice_number"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    @error('invoice_number')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Invoice Date --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha Factura *</label>
                    <input
                        type="date"
                        wire:model="invoice_date"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    @error('invoice_date')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Received Date --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha Recibida *</label>
                    <input
                        type="date"
                        wire:model="received_date"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    @error('received_date')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Due Date --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha Vencimiento *</label>
                    <input
                        type="date"
                        wire:model="due_date"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    @error('due_date')
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
                        <option value="PAB">Panamá (PAB)</option>
                        <option value="USD">Dólar (USD)</option>
                    </select>
                </div>
            </div>

            {{-- Amounts --}}
            <div class="space-y-2 p-4 bg-blue-50 rounded-lg">
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Subtotal *</label>
                        <input
                            type="number"
                            step="0.01"
                            wire:model.live="subtotal"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">ITBMS (Impuesto)</label>
                        <input
                            type="number"
                            step="0.01"
                            wire:model.live="tax_amount"
                            class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Total *</label>
                        <div class="px-3 py-2 bg-gray-100 border rounded-lg text-sm font-semibold">
                            {{ number_format($this->total_amount, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cost Center --}}
            <div>
                <label class="block text-sm font-medium mb-1">Centro de Costo</label>
                <select
                    wire:model="cost_center_id"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="">Sin centro de costo específico</option>
                    @foreach ($costCenters as $center)
                        <option value="{{ $center->id }}">
                            {{ $center->code }} - {{ $center->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Cost Distribution --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium">Distribución de Costos</label>
                    <button
                        type="button"
                        wire:click="addDistribution"
                        class="text-sm text-blue-600 hover:text-blue-900"
                    >
                        + Agregar
                    </button>
                </div>

                @if (! empty($distributions))
                    <div class="space-y-2">
                        @foreach ($distributions as $index => $dist)
                            <div class="flex gap-2 items-start">
                                <select
                                    wire:model.live="distributions.{{ $index }}.cost_center_id"
                                    class="flex-1 px-3 py-2 border rounded-lg"
                                >
                                    <option value="0">Seleccionar...</option>
                                    @foreach ($costCenters as $center)
                                        <option value="{{ $center->id }}">
                                            {{ $center->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <input
                                    type="number"
                                    step="0.01"
                                    max="100"
                                    wire:model.live="distributions.{{ $index }}.percentage"
                                    placeholder="%"
                                    class="w-20 px-3 py-2 border rounded-lg"
                                />

                                <button
                                    type="button"
                                    wire:click="removeDistribution({{ $index }})"
                                    class="text-red-600 hover:text-red-900"
                                >
                                    ✕
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium mb-1">Notas</label>
                <textarea
                    wire:model="notes"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="3"
                ></textarea>
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
