<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        {{-- Header --}}
        <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
            <h3 class="text-lg font-bold">
                Programa de Pagos - Factura {{ $invoice->invoice_number }}
            </h3>
            <button
                wire:click="$set('showModal', false)"
                class="text-gray-500 hover:text-gray-700"
            >
                ✕
            </button>
        </div>

        {{-- Body --}}
        <div class="p-6 space-y-4">
            {{-- Invoice Info --}}
            <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-lg">
                <div>
                    <span class="text-sm text-gray-600">Proveedor:</span>
                    <p class="font-semibold">{{ $invoice->supplier->legal_name }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Total Factura:</span>
                    <p class="font-semibold">{{ number_format($invoice->total_amount, 2) }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Ya Pagado:</span>
                    <p class="font-semibold">{{ number_format($invoice->paid_amount, 2) }}</p>
                </div>
                <div>
                    <span class="text-sm text-gray-600">Saldo Pendiente:</span>
                    <p class="font-semibold">{{ number_format($this->remainingAmount, 2) }}</p>
                </div>
            </div>

            {{-- Schedule Table --}}
            <form wire:submit.prevent="save">
                <div class="overflow-x-auto">
                    <table class="w-full border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-sm font-semibold">Fecha Pago</th>
                                <th class="px-4 py-2 text-right text-sm font-semibold">Monto</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold">Estado</th>
                                <th class="px-4 py-2 text-center text-sm font-semibold">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($schedules as $index => $schedule)
                                <tr>
                                    <td class="px-4 py-2">
                                        <input
                                            type="date"
                                            wire:model="schedules.{{ $index }}.payment_date"
                                            class="w-full px-2 py-1 border rounded text-sm"
                                        />
                                    </td>
                                    <td class="px-4 py-2">
                                        <input
                                            type="number"
                                            step="0.01"
                                            wire:model.live="schedules.{{ $index }}.amount"
                                            class="w-full px-2 py-1 border rounded text-sm text-right"
                                        />
                                    </td>
                                    <td class="px-4 py-2 text-center text-sm">
                                        @if ($schedule['paid'])
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
                                                Pagado
                                            </span>
                                        @else
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">
                                                Pendiente
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-center">
                                        <button
                                            type="button"
                                            wire:click="removeSchedule({{ $index }})"
                                            class="text-red-600 hover:text-red-900 text-sm"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        No hay pagos programados
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Add Schedule Button --}}
                <div class="mt-4">
                    <button
                        type="button"
                        wire:click="addSchedule"
                        class="px-4 py-2 text-blue-600 hover:text-blue-900 text-sm border border-blue-600 rounded"
                    >
                        + Agregar Cuota
                    </button>
                </div>

                {{-- Remaining Info --}}
                <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Monto Restante por Programar:</p>
                    <p class="text-xl font-semibold text-blue-600">
                        {{ number_format($this->remainingAmount, 2) }}
                    </p>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-2 mt-6">
                    <button
                        type="button"
                        wire:click="$set('showModal', false)"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-50"
                    >
                        Cancelar
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                    >
                        Guardar Programa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
