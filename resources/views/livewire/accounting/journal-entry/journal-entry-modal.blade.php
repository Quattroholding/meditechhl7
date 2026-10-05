@if($isModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg max-w-5xl w-full max-h-[95vh] overflow-y-auto">
            <div class="sticky top-0 bg-light px-6 py-4 border-b d-flex justify-content-between align-items-center">
                <h3 class="text-lg font-bold">
                    {{ $this->entry ? __('accounting.journal_entry.edit_title') : __('accounting.journal_entry.create_title') }}
                </h3>
                <button
                    type="button"
                    wire:click="$dispatch('closeModal')"
                    class="text-gray-500 hover:text-gray-700"
                    style="border: none; background: none; cursor: pointer;"
                >
                    ×
                </button>
            </div>

            <form wire:submit.prevent="save" class="p-6">
@else
    <form wire:submit.prevent="save">
@endif
                <div class="row">
                    <!-- Entry Date -->
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="entry_date" :value="__('accounting.journal_entry.date')" required="true"/>
                            <x-text-input wire:model="entry_date" id="entry_date" class="block mt-1 w-full" type="date" name="entry_date"/>
                            <x-input-error :messages="$errors->get('entry_date')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Document Type -->
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="document_type" :value="__('accounting.journal_entry.document_type')" required="true"/>
                            <x-select-input wire:model="document_type" id="document_type" name="document_type" :options="[
                                'manual' => __('accounting.journal_entry.types.manual'),
                                'invoice' => __('accounting.journal_entry.types.invoice'),
                                'payment' => __('accounting.journal_entry.types.payment'),
                                'adjustment' => __('accounting.journal_entry.types.adjustment'),
                            ]" :selected="[null]" class="block w-full"/>
                            <x-input-error :messages="$errors->get('document_type')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Document Number -->
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="document_number" :value="__('accounting.journal_entry.document_number')" required="true"/>
                            <x-text-input wire:model="document_number" id="document_number" class="block mt-1 w-full" type="text" name="document_number" placeholder="Ej: DOC-001"/>
                            <x-input-error :messages="$errors->get('document_number')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="col-12 col-md-6">
                        <div class="input-block local-forms">
                            <x-input-label for="description" :value="__('accounting.journal_entry.description')" required="true"/>
                            <x-text-input wire:model="description" id="description" class="block mt-1 w-full" type="text" name="description" placeholder="Descripción del asiento"/>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
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

                <div class="doctor-submit text-end">
                    @if ($this->entry && $this->entry->isDraft())
                        <button type="button" wire:click="delete" wire:confirm="{{ __('accounting.journal_entry.confirm_delete') }}" class="btn btn-danger me-2">
                            {{ __('button.delete') }}
                        </button>
                    @endif
                    <button type="submit" @if(!$isBalanced) disabled @endif class="btn btn-primary me-2" @if(!$isBalanced) style="opacity: 0.5; cursor: not-allowed;" @endif>
                        {{ __('button.save') }}
                    </button>
                    @if($isModal)
                        <button type="button" wire:click="$dispatch('closeModal')" class="btn btn-secondary">
                            {{ __('button.cancel') }}
                        </button>
                    @endif
                </div>
            </form>
        @if($isModal)
            </div>
        </div>
        @endif
