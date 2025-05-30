<div>
    <button wire:click="openModal()" class="btn-head btn-head-light">  📚 {{__('Agregar historial')}}</button>
    @if($showModal)
    <!-- Modal -->
    <div class="modal fade show" id="bs-example-modal-lg" tabindex="1000"
         aria-labelledby="myLargeModalLabel"
         style="display: block;" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel" style="color: #000;">{{__('Agregar Historia Medica')}} : {{ $patient->name }}</h4>
                    <button wire:click="$set('showModal', false)" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="input-block  local-forms">
                        <x-input-label for="categoria" :value="__('patient.category')" required="true"/>
                        <x-select-input wire:model.defer="category" :options="\App\Models\Lista::medicalHistoryCategory()" :selected="['allergy']" name="category" class="block mt-1 w-full"/>
                        @error('category') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-block local-forms">
                        <div class="form-group local-forms">
                            <x-input-label for="titulo" :value="__('patient.history_title')" required="true"/>
                            <x-text-input wire:model.defer="title" class="block mt-1 w-full datetimepicker" type="text" name="title" placeholder="Ejemplo : Mariscos,Apendicitis,Tabquismo,etc."/>
                            @error('title') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="input-block local-forms">
                        <div class="form-group local-forms">
                            <x-input-label for="fecha_occurencia" :value="__('patient.date')" required="true"/>
                            <x-text-input wire:model.defer="occurrence_date" class="block mt-1 w-full datetimepicker" type="date" name="occurrence_date"/>
                            @error('occurrence_date') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="input-block local-forms">
                        <x-input-label for="descripcion" :value="__('patient.description')" />
                        <x-textarea-input wire:model.defer="description" class="block mt-1 w-full" name="description"/>
                    </div>

                </div>
                <div class="modal-footer">
                    <button  wire:click="$set('showModal', false)" type="button" class="btn btn-danger cancel-form" data-bs-dismiss="modal">{{ __('generic.cancel') }}</button>
                    <button  wire:click="save" type="button" class="btn btn-primary submit-form me-2">{{ __('generic.save') }}</button>
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
