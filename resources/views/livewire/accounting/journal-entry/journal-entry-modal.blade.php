<form wire:submit.prevent="save">
@csrf
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
    <div class="border-top pt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Líneas del Asiento</h4>
            <button
                type="button"
                wire:click="addLine()"
                class="btn btn-success btn-sm"
            >
                <i class="fa-solid fa-plus me-1"></i>Agregar Línea
            </button>
        </div>

        {{-- Lines Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Cuenta Contable</th>
                        <th>Centro de Costo</th>
                        <th class="text-end" style="width: 80px;">Débito</th>
                        <th class="text-end" style="width: 80px;">Crédito</th>
                        <th>Descripción</th>
                        <th style="width: 50px;">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($lines as $index => $line)
                        <tr>
                            <td>
                                <x-select-input wire:model.live="lines.{{ $index }}.accounting_account_id" name="accounting_account_id" :options="\App\Models\AccountingAccount::pluck('name','code')->toArray()" :selected="[]" class="block mt-1 w-full"/>

                                @error("lines.{$index}.accounting_account_id")
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>

                                <x-select-input wire:model.live="lines.{{ $index }}.cost_center_id" name="id_type" :options="\App\Models\CostCenter::pluck('name','code')->toArray()" :selected="[]" class="block mt-1 w-full"/>
                            </td>
                            <td class="text-end">
                                <input
                                    type="number"
                                    wire:model.live="lines.{{ $index }}.debit"
                                    step="0.01"
                                    min="0"
                                    class="form-control form-control-sm"
                                    placeholder="0.00"
                                />
                                @error("lines.{$index}.debit")
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="text-end">
                                <input
                                    type="number"
                                    wire:model.live="lines.{{ $index }}.credit"
                                    step="0.01"
                                    min="0"
                                    class="form-control form-control-sm"
                                    placeholder="0.00"
                                />
                                @error("lines.{$index}.credit")
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input
                                    type="text"
                                    wire:model="lines.{{ $index }}.description"
                                    class="form-control form-control-sm"
                                    placeholder="Desc..."
                                />
                            </td>
                            <td class="text-center">
                                @if (count($lines) > 2)
                                    <button
                                        type="button"
                                        wire:click="removeLine({{ $index }})"
                                        class="btn btn-danger btn-sm"
                                        title="Eliminar"
                                    >
                                        ✕
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                No hay líneas. Agregue al menos una.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr class="fw-semibold">
                        <td colspan="2" class="text-end">Totales:</td>
                        <td class="text-end">
                            {{ number_format($totalDebit, 2, '.', ',') }}
                        </td>
                        <td class="text-end">
                            {{ number_format($totalCredit, 2, '.', ',') }}
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Balance Status --}}
        <div class="mt-3 p-3 rounded {{ $isBalanced ? 'alert alert-success' : 'alert alert-danger' }}">
            @if ($isBalanced)
                <i class="fas fa-check-circle me-2"></i>
                <strong>Asiento Balanceado</strong>
            @else
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Desequilibrio: {{ number_format(abs($totalDebit - $totalCredit), 2) }}</strong>
            @endif
        </div>

        @error('lines')
            <div class="text-danger small mt-2">{{ $message }}</div>
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
            @if(!$isModal)
                <a class="btn btn-secondary cancel-form" href="{{ route('accounting.journal-entries') }}">  {{ __('button.cancel') }}</a>
            @endif
        </div>
</form>

