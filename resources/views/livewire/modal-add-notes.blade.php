<div>
    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h2 class="modal-title">Agregar nota</h2>
                <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
            </div>
            <form wire:submit="saveNote">
                <div class="input-block local-forms">
                    <label class="form-label">{{__('Nota')}}</label>
                    <textarea wire:model="note" class="form-control-full" rows="3" placeholder="Escribir nota"></textarea>
                    <x-input-error :messages="$errors->get('note')"/>
                </div>
                <div style="margin-top: 30px; display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                       {{__('generic.save')}}
                    </button>
                </div>
            </form>
        </div>
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
