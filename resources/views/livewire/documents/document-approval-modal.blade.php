<div>
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 bg-gray-50 border-b border-gray-200 px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ $document?->original_filename }}</h3>
                    <p class="text-sm text-gray-500">Tipo: {{ $document?->document_type->label() }}</p>
                </div>
                <button
                    wire:click="closeModal"
                    class="text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content Grid: PDF (left) + Items (right) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
                <!-- PDF Preview -->
                <div class="border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                    @if ($pdfUrl)
                        <embed src="{{ $pdfUrl }}" type="application/pdf" class="w-full" style="height: 600px;" />
                    @else
                        <div class="h-96 flex items-center justify-center">
                            <div class="text-center text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M8 16.5a1 1 0 11-2 0 1 1 0 012 0zM15 7a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p>Documento no disponible</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Items Table -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-4">Datos Extraídos</h4>

                    @if ($validationMessages)
                        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-xs font-medium text-yellow-800 mb-2">Advertencias:</p>
                            <ul class="text-xs text-yellow-700 space-y-1">
                                @foreach ($validationMessages as $message)
                                    <li>• {{ $message }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-4 flex items-center gap-2">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full">
                            <div
                                class="h-2 bg-blue-600 rounded-full"
                                style="width: {{ $confidenceScore * 100 }}%"
                            ></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700">{{ round($confidenceScore * 100) }}%</span>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-3 py-2 text-left">
                                        <input type="checkbox" class="rounded" wire:model="selectedItems" />
                                    </th>
                                    <th class="px-3 py-2 text-left">SKU</th>
                                    <th class="px-3 py-2 text-left">Nombre</th>
                                    <th class="px-3 py-2 text-right">Cantidad</th>
                                    <th class="px-3 py-2 text-right">Costo</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse ($items as $index => $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-3 py-2">
                                            <input
                                                type="checkbox"
                                                wire:model="selectedItems.{{ $index }}"
                                                class="rounded"
                                            />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input
                                                type="text"
                                                value="{{ $item['sku'] ?? '' }}"
                                                wire:change="updateItemField({{ $index }}, 'sku', $event.target.value)"
                                                class="w-24 px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                            />
                                        </td>
                                        <td class="px-3 py-2">
                                            <input
                                                type="text"
                                                value="{{ $item['name'] ?? '' }}"
                                                wire:change="updateItemField({{ $index }}, 'name', $event.target.value)"
                                                class="flex-1 px-2 py-1 border border-gray-300 rounded text-xs focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                placeholder="Nombre"
                                            />
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input
                                                type="number"
                                                value="{{ $item['quantity'] ?? '' }}"
                                                wire:change="updateItemField({{ $index }}, 'quantity', $event.target.value)"
                                                class="w-16 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                min="0"
                                            />
                                        </td>
                                        <td class="px-3 py-2 text-right">
                                            <input
                                                type="number"
                                                value="{{ $item['unit_cost'] ?? '' }}"
                                                wire:change="updateItemField({{ $index }}, 'unit_cost', $event.target.value)"
                                                class="w-20 px-2 py-1 border border-gray-300 rounded text-xs text-right focus:outline-none focus:ring-1 focus:ring-blue-500"
                                                step="0.01"
                                                min="0"
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-3 py-4 text-center text-gray-500 text-sm">
                                            No hay items
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="border-t border-gray-200 px-6 py-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas (opcional)</label>
                <textarea
                    wire:model="notes"
                    placeholder="Añade notas sobre este documento..."
                    rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                ></textarea>
            </div>

            <!-- Rejection Form (if showing) -->
            @if ($isRejecting)
                <div class="border-t border-gray-200 px-6 py-4 bg-red-50">
                    <h4 class="font-semibold text-red-900 mb-3">Rechazar Documento</h4>
                    <textarea
                        wire:model="rejectionReason"
                        placeholder="Explica por qué rechazas este documento (mínimo 10 caracteres)..."
                        rows="3"
                        class="w-full px-3 py-2 border border-red-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-500"
                    ></textarea>
                    @error('rejectionReason') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            @endif

            <!-- Actions -->
            <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-6 py-4 flex items-center justify-between gap-3">
                <button
                    type="button"
                    wire:click="closeModal"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors"
                >
                    Cancelar
                </button>

                <div class="flex gap-3">
                    @if ($isRejecting)
                        <button
                            type="button"
                            wire:click="cancelRejection"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors"
                        >
                            Volver
                        </button>
                        <button
                            type="button"
                            wire:click="confirmReject"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors"
                        >
                            Confirmar Rechazo
                        </button>
                    @else
                        <button
                            type="button"
                            wire:click="startRejecting"
                            class="px-4 py-2 border border-red-300 rounded-lg text-red-700 font-medium hover:bg-red-50 transition-colors"
                        >
                            Rechazar
                        </button>
                        <button
                            type="button"
                            wire:click="approve"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors"
                        >
                            Aprobar
                        </button>
                    @endif
                </div>
            </div>
            </div>
        </div>
    @endif
</div>
