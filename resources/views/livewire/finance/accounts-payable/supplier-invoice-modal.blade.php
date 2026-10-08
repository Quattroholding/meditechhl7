<form wire:submit.prevent="save">
   @csrf
    <div class="row">
        <!-- Supplier -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="supplier_id" :value="__('finance.suppliers.title')" required="true"/>
                <select wire:model="supplier_id" id="supplier_id" class="form-control">
                    <option value="">{{ __('generic.select') }}</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}">
                            {{ $supplier->legal_name }} ({{ $supplier->ruc }})
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('supplier_id')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Invoice Number -->
        <div class="col-12 col-md-4">
            <div class="input-block local-forms">
                <x-input-label for="invoice_number" :value="__('finance.supplier_invoices.invoice_number')" required="true"/>
                <x-text-input wire:model="invoice_number" id="invoice_number" class="block mt-1 w-full" type="text" name="invoice_number"/>
                <x-input-error :messages="$errors->get('invoice_number')" class="mt-2" />
            </div>
        </div>

        <!-- Invoice Date -->
        <div class="col-12 col-md-4">
            <div class="input-block local-forms">
                <x-input-label for="invoice_date" :value="__('finance.supplier_invoices.invoice_date')" required="true"/>
                <x-text-input wire:model="invoice_date" id="invoice_date" class="block mt-1 w-full" type="date" name="invoice_date"/>
                <x-input-error :messages="$errors->get('invoice_date')" class="mt-2" />
            </div>
        </div>

        <!-- Received Date -->
        <div class="col-12 col-md-4">
            <div class="input-block local-forms">
                <x-input-label for="received_date" :value="__('finance.supplier_invoices.received_date')" required="true"/>
                <x-text-input wire:model="received_date" id="received_date" class="block mt-1 w-full" type="date" name="received_date"/>
                <x-input-error :messages="$errors->get('received_date')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Due Date -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="due_date" :value="__('finance.supplier_invoices.due_date')" required="true"/>
                <x-text-input wire:model="due_date" id="due_date" class="block mt-1 w-full" type="date" name="due_date"/>
                <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
            </div>
        </div>

        <!-- Currency -->
        <div class="col-12 col-md-6">
            <div class="input-block local-forms">
                <x-input-label for="currency" :value="__('finance.supplier_invoices.currency')" required="true"/>
                <x-select-input wire:model="currency" id="currency" name="currency" :options="[
                    'USD' => __('finance.currency.usd'),
                    'PAB' => __('finance.currency.pab'),
                ]" :selected="['USD']" class="block w-full"/>
                <x-input-error :messages="$errors->get('currency')" class="mt-2" />
            </div>
        </div>
    </div>

    <!-- Amounts Section -->
    <div class="row">
        <div class="col-12">
            <div class="alert alert-light" role="alert">
                <div class="row">
                    <div class="col-12 col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="subtotal" :value="__('finance.supplier_invoices.subtotal')" required="true"/>
                            <x-text-input wire:model.live="subtotal" id="subtotal" class="block mt-1 w-full" type="number" name="subtotal" step="0.01"/>
                            <x-input-error :messages="$errors->get('subtotal')" class="mt-2" />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="tax_amount" :value="__('finance.supplier_invoices.tax_amount')"/>
                            <x-text-input wire:model.live="tax_amount" id="tax_amount" class="block mt-1 w-full" type="number" name="tax_amount" step="0.01"/>
                            <x-input-error :messages="$errors->get('tax_amount')" class="mt-2" />
                        </div>
                    </div>

                    <div class="col-12 col-md-4">
                        <div class="input-block local-forms">
                            <x-input-label for="total" :value="__('finance.supplier_invoices.total_amount')" required="true"/>
                            <div class="form-control" style="background-color: #f8f9fa; cursor: not-allowed;">
                                {{ number_format($this->total_amount, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Cost Center -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="cost_center_id" :value="__('finance.supplier_invoices.cost_center')"/>
                <select wire:model="cost_center_id" id="cost_center_id" class="form-control">
                    <option value="">{{ __('finance.supplier_invoices.no_cost_center') }}</option>
                    @foreach ($costCenters as $center)
                        <option value="{{ $center->id }}">
                            {{ $center->code }} - {{ $center->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('cost_center_id')" class="mt-2" />
            </div>
        </div>
    </div>

    <!-- Cost Distribution -->
    <div class="row">
        <div class="col-12">
            <div class="input-block local-forms">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <x-input-label :value="__('finance.supplier_invoices.cost_distribution')"/>
                    <button type="button" wire:click="addDistribution" class="btn btn-sm btn-outline-primary">
                        {{ __('button.add') }}
                    </button>
                </div>

                @if (! empty($distributions))
                    <div class="space-y-2">
                        @foreach ($distributions as $index => $dist)
                            <div class="row">
                                <div class="col-12 col-md-9">
                                    <select wire:model.live="distributions.{{ $index }}.cost_center_id" class="form-control">
                                        <option value="">{{ __('generic.select') }}</option>
                                        @foreach ($costCenters as $center)
                                            <option value="{{ $center->id }}">
                                                {{ $center->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12 col-md-2">
                                    <x-text-input type="number" step="0.01" max="100" wire:model.live="distributions.{{ $index }}.percentage" placeholder="%"/>
                                </div>

                                <div class="col-12 col-md-1">
                                    <button type="button" wire:click="removeDistribution({{ $index }})" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Notes -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="notes" :value="__('generic.notes')"/>
                <x-textarea-input wire:model="notes" id="notes" class="block mt-1 w-full" name="notes"/>
                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Invoice File Upload -->
        <div class="col-12">
            <div class="input-block local-forms">
                <x-input-label for="invoice_file" :value="__('finance.supplier_invoices.invoice_file')"/>
                <div class="form-control p-3 text-center bg-light">
                    <input
                        type="file"
                        id="invoice_file"
                        wire:model="invoice_file"
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="form-control-file"
                    />
                    <small class="text-muted d-block mt-2">
                        {{ __('finance.supplier_invoices.file_help') }}
                    </small>
                </div>
                @if($invoice_file)
                    <div class="alert alert-info mt-2">
                        <i class="fas fa-file"></i> {{ $invoice_file->getClientOriginalName() }}
                        <button type="button" wire:click="$set('invoice_file', null)" class="btn btn-sm btn-outline-danger float-end">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                @endif
                <x-input-error :messages="$errors->get('invoice_file')" class="mt-2" />
            </div>
        </div>
    </div>

    <div class="doctor-submit text-end">
        <button type="submit" class="btn btn-primary me-2">
            {{ __('button.save') }}
        </button>
        @if($isModal)
            <button type="button" wire:click="$dispatch('closeModal')" class="btn btn-secondary">
                {{ __('button.cancel') }}
            </button>
        @endif
        @if(!$isModal)
            <a class="btn btn-secondary cancel-form" href="{{ route('finance.payables.invoices.index') }}">  {{ __('button.cancel') }}</a>
        @endif
    </div>
</form>

