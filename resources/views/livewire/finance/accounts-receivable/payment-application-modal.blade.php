<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-md w-full">
        {{-- Header --}}
        <div class="bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">Aplicar Pago</h3>
            <button
                wire:click="$dispatch('closePaymentModal')"
                class="text-gray-500 hover:text-gray-700"
            >
                ✕
            </button>
        </div>

        {{-- Body --}}
        <form wire:submit.prevent="apply" class="p-6 space-y-4">
            {{-- Invoice Info --}}
            <div class="space-y-2 p-3 bg-blue-50 rounded-lg text-sm">
                <p><span class="font-semibold">Factura:</span> {{ $receivable->invoice_number }}</p>
                <p><span class="font-semibold">Paciente:</span> {{ $receivable->patient->full_name ?? 'N/A' }}</p>
                <p><span class="font-semibold">Total:</span> {{ number_format($receivable->original_amount, 2) }}</p>
                <p><span class="font-semibold">Saldo Pendiente:</span> {{ number_format($receivable->balance, 2) }}</p>
            </div>

            {{-- Amount Input --}}
            <div>
                <label class="block text-sm font-medium mb-1">Monto a Aplicar *</label>
                <input
                    type="number"
                    step="0.01"
                    wire:model="amount_to_apply"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    max="{{ $receivable->balance }}"
                />
                @error('amount_to_apply')
                    <span class="text-red-600 text-xs">{{ $message }}</span>
                @enderror
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-medium mb-1">Notas</label>
                <textarea
                    wire:model="notes"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    rows="2"
                ></textarea>
            </div>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 pt-4">
                <button
                    type="button"
                    wire:click="$dispatch('closePaymentModal')"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-50"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                    Aplicar Pago
                </button>
            </div>
        </form>
    </div>
</div>
