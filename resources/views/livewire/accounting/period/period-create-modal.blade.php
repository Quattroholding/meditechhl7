<div>
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div class="modal-content" wire:click.stop style="position: relative; max-width: 800px; width: 90%; max-height: 90vh; overflow-y: auto; background: white; border-radius: 8px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
                <div class="modal-header" style="padding: 1.5rem; border-bottom: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items: center;">
                    <h5 class="modal-title" id="periodCreateModalLabel" style="margin: 0;">
                        <i class="fas fa-calendar-plus"></i> Crear Nuevo Período Contable
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="padding: 1.5rem;">
                    <form wire:submit="save">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="fiscal_year" :value="__('Año Fiscal')" required="true" />
                                    <x-text-input wire:model.live="fiscal_year" id="fiscal_year" type="number" min="2020"
                                                  class="form-control" />
                                    <x-input-error :messages="$errors->get('fiscal_year')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="period_number" :value="__('Mes')" required="true" />
                                    <select wire:model.live="period_number" id="period_number" class="form-control">
                                        <option value="">Seleccionar mes</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}">
                                                {{ Carbon\Carbon::createFromDate(2026, $i, 1)->locale('es')->translatedFormat('F') }}
                                            </option>
                                        @endfor
                                    </select>
                                    <x-input-error :messages="$errors->get('period_number')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="input-block local-forms">
                                    <x-input-label for="name" :value="__('Nombre del Período')" required="true" />
                                    <x-text-input wire:model="name" id="name" type="text" class="form-control"
                                                  placeholder="Ej: Octubre 2026" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="start_date" :value="__('Fecha de Inicio')" required="true" />
                                    <x-text-input wire:model="start_date" id="start_date" type="date"
                                                  class="form-control" />
                                    <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-block local-forms">
                                    <x-input-label for="end_date" :value="__('Fecha de Fin')" required="true" />
                                    <x-text-input wire:model="end_date" id="end_date" type="date"
                                                  class="form-control" />
                                    <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-save"></i> Crear Período
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

