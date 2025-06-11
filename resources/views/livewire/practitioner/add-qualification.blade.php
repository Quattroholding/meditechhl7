<div>
    <button wire:click="openModal()" class="btn-head btn-head-light">  📚 {{__('Agregar Especialidad')}}</button>
    @if($showModal)
        <!-- Modal -->
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop style="max-width: 500px;">
                <div class="modal-header">
                    <h2 class="modal-title" id="myLargeModalLabel" style="color: #000;">{{__('Agregar Especialidad')}} : <br/>{{ $practitoner->name }}</h2>
                    <button wire:click="$set('showModal', false)" type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="input-block  local-forms">
                    <x-input-label for="medical_speciality_id" :value="__('doctor.qualification')" />
                    <x-select-input wire:model.defer="medical_speciality_id" :options="\App\Models\MedicalSpeciality::pluck('name','id')->toArray()" :selected="[]" name="medical_speciality_id" class="block mt-1 w-full"/>
                    <x-input-error :messages="$errors->get('medical_speciality_id')"/>
                </div>
                <p style="color: #000;text-align: center;padding: 15px;">{{__('doctor.qualification_period')}}</p>
                <div class="row">
                    <div class="col-6 col-md-6 col-xl-6">
                        <div class="input-block  local-forms">
                            <x-input-label for="id_type" :value="__('doctor.qualification_start')" />
                            <x-text-input wire:model.defer="period_start" class="block mt-1 w-full datetimepicker" type="date" name="period_start"/>
                            <x-input-error :messages="$errors->get('period_start')"/>
                        </div>
                    </div>
                    <div class="col-6 col-md-6 col-xl-6">
                        <div class=" input-block  local-forms ">
                            <x-input-label for="period_end" :value="__('doctor.qualification_end')" />
                            <x-text-input wire:model.defer="period_end" class="block mt-1 w-full datetimepicker" type="date" name="period_end"/>
                            <x-input-error :messages="$errors->get('period_end')"/>
                        </div>
                    </div>
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
