<div>
    <button wire:click="openModal()" class="btn-head btn-head-light">  📚 {{__('Agregar historial')}}</button>
    @if($showModal)
    <!-- Modal -->
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop style="max-width: 400px;">
                <div class="modal-header">
                    <h2 class="modal-title" id="myLargeModalLabel" style="color: #000;">{{__('Agregar Historia Medica')}} : {{ $patient->name }}</h2>
                    <button wire:click="$set('showModal', false)" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="input-block  local-forms">
                    <x-input-label for="categoria" :value="__('patient.category')" required="true"/>
                    <x-select-input wire:model.defer="category" :options="\App\Models\Lista::medicalHistoryCategory()" :selected="['allergy']" name="category" class="block mt-1 w-full"/>
                    <x-input-error :messages="$errors->get('category')"/>
                </div>
                <div class="input-block local-forms">
                    <div class="form-group local-forms">
                        <x-input-label for="titulo" :value="__('patient.history_title')" required="true"/>
                        <x-text-input wire:model.defer="title" class="block mt-1 w-full datetimepicker" type="text" name="title" placeholder="Ejemplo : Mariscos,Apendicitis,Tabquismo,etc."/>
                        <x-input-error :messages="$errors->get('title')"/>
                    </div>
                </div>
                <div class="input-block local-forms">
                    <div class="form-group local-forms">
                        <x-input-label for="fecha_occurencia" :value="__('patient.date')" required="true"/>
                        <x-text-input wire:model.defer="occurrence_date" class="block mt-1 w-full datetimepicker" type="date" name="occurrence_date"/>
                        <x-input-error :messages="$errors->get('occurrence_date')"/>
                    </div>
                </div>
                <div class="input-block local-forms">
                    <x-input-label for="descripcion" :value="__('patient.description')" />
                    <x-textarea-input wire:model.defer="description" class="block mt-1 w-full" name="description"/>
                    <x-input-error :messages="$errors->get('description')"/>
                </div>
                <div style="margin-top: 20px; display: flex; gap: 15px;">
                    <button wire:click="save" class="btn btn-primary" style="flex: 1;">{{ __('generic.save') }}</button>
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary">{{ __('generic.cancel') }}</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });
    </script>
</div>
