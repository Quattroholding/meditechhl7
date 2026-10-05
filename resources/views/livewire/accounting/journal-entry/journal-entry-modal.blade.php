@if($isModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-5xl w-full max-h-[95vh] overflow-y-auto">
            {{-- Header --}}
            <div class="sticky top-0 bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold">
                    {{ $this->entry ? 'Editar Asiento Contable' : 'Nuevo Asiento Contable' }}
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
            {{-- Header Section --}}
            <div class="grid grid-cols-2 gap-4">
                {{-- Entry Date --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Fecha del Asiento *</label>
                    <input
                        type="date"
                        wire:model="entry_date"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    @error('entry_date')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Document Type --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Tipo de Documento *</label>
                    <select
                        wire:model="document_type"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Seleccionar tipo...</option>
                        <option value="manual">Manual</option>
                        <option value="invoice">Factura</option>
                        <option value="payment">Pago</option>
                        <option value="adjustment">Ajuste</option>
                    </select>
                    @error('document_type')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Document Number --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Número de Documento *</label>
                    <input
                        type="text"
                        wire:model="document_number"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Ej: DOC-001"
                    />
                    @error('document_number')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Descripción *</label>
                    <input
                        type="text"
                        wire:model="description"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Descripción del asiento"
                    />
                    @error('description')
                        <span class="text-red-600 text-xs">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Lines Section --}}
            <div class="border-t pt-4">
                <div class="flex justify-between items-center mb-3">
                    <h4 class="text-md font-semibold">Líneas del Asiento</h4>
                    <button
                        type="button"
                        wire:click="addLine"
                        class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
                    >
                        + Agregar Línea
                    </button>
                </div>

                {{-- Lines Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="border px-2 py-2 text-left">Cuenta Contable</th>
                                <th class="border px-2 py-2 text-left">Centro de Costo</th>
                                <th class="border px-2 py-2 text-right w-24">Débito</th>
                                <th class="border px-2 py-2 text-right w-24">Crédito</th>
                                <th class="border px-2 py-2 text-left">Descripción</th>
                                <th class="border px-2 py-2 text-center w-12">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($lines as $index => $line)
                                <tr class="hover:bg-gray-50">
                                    <td class="border px-2 py-2">
                                        <select
                                            wire:model="lines.{{ $index }}.accounting_account_id"
                                            class="w-full px-2 py-1 border rounded text-xs"
                                        >
                                            <option value="0">Seleccionar...</option>
                                            @foreach ($accounts as $account)
                                                <option value="{{ $account->id }}">
                                                    {{ $account->code }} - {{ $account->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("lines.{$index}.accounting_account_id")
                                            <span class="text-red-600 text-xs block">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="border px-2 py-2">
                                        <select
                                            wire:model="lines.{{ $index }}.cost_center_id"
                                            class="w-full px-2 py-1 border rounded text-xs"
                                        >
                                            <option value="">Ninguno</option>
                                            @foreach ($costCenters as $center)
                                                <option value="{{ $center->id }}">
                                                    {{ $center->code }} - {{ $center->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border px-2 py-2 text-right">
                                        <input
                                            type="number"
                                            wire:model.live="lines.{{ $index }}.debit"
                                            step="0.01"
                                            min="0"
                                            class="w-full px-2 py-1 border rounded text-xs text-right"
                                            placeholder="0.00"
                                        />
                                        @error("lines.{$index}.debit")
                                            <span class="text-red-600 text-xs block">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="border px-2 py-2 text-right">
                                        <input
                                            type="number"
                                            wire:model.live="lines.{{ $index }}.credit"
                                            step="0.01"
                                            min="0"
                                            class="w-full px-2 py-1 border rounded text-xs text-right"
                                            placeholder="0.00"
                                        />
                                        @error("lines.{$index}.credit")
                                            <span class="text-red-600 text-xs block">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="border px-2 py-2">
                                        <input
                                            type="text"
                                            wire:model="lines.{{ $index }}.description"
                                            class="w-full px-2 py-1 border rounded text-xs"
                                            placeholder="Desc..."
                                        />
                                    </td>
                                    <td class="border px-2 py-2 text-center">
                                        @if (count($lines) > 2)
                                            <button
                                                type="button"
                                                wire:click="removeLine({{ $index }})"
                                                class="text-red-600 hover:text-red-800 font-bold"
                                            >
                                                ✕
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="border px-2 py-4 text-center text-gray-500">
                                        No hay líneas. Agregue al menos una.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 font-semibold">
                                <td colspan="2" class="border px-2 py-2 text-right">Totales:</td>
                                <td class="border px-2 py-2 text-right">
                                    {{ number_format($totalDebit, 2, '.', ',') }}
                                </td>
                                <td class="border px-2 py-2 text-right">
                                    {{ number_format($totalCredit, 2, '.', ',') }}
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Balance Status --}}
                <div class="mt-2 p-2 rounded {{ $isBalanced ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    @if ($isBalanced)
                        <span class="text-sm font-semibold">✓ Asiento Balanceado</span>
                    @else
                        <span class="text-sm font-semibold">✗ Desequilibrio: {{ number_format(abs($totalDebit - $totalCredit), 2) }}</span>
                    @endif
                </div>

                @error('lines')
                    <span class="text-red-600 text-xs block mt-1">{{ $message }}</span>
                @enderror
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
                @if ($this->entry && $this->entry->isDraft())
                    <button
                        type="button"
                        wire:click="delete"
                        wire:confirm="¿Está seguro de que desea eliminar este asiento?"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                    >
                        Eliminar
                    </button>
                @endif
                <button
                    type="submit"
                    @if(!$isBalanced) disabled @endif
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    Guardar
                </button>
            </div>
            </form>
        @if($isModal)
            </div>
        </div>
        @endif
