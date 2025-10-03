<div>
    @if($showModal)
    <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
        <div class="modal-content" wire:click.stop>
            <div class="modal-header">
                <h2 class="modal-title">Agregar nota</h2>
                <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;color: #0c0c0c;">&times;</button>
            </div>
            <form wire:submit="saveNote">
                <div class="input-block local-forms">
                    <textarea wire:model="note" class="form-control-full" rows="3" placeholder="Escribir nota" style="color: #0c0c0c"></textarea>
                    <x-input-error :messages="$errors->get('note')"/>
                </div>
                <div style="margin-top: 20px;" class="d-flex flex-wrap gap-2">
                    <button  type="submit" class="btn btn-primary" style="flex: 1;">{{ __('generic.save') }}</button>
                    <button type="button" wire:click="$set('showModal', false)" class="btn btn-secondary" style="flex: 1;">{{ __('generic.cancel') }}</button>
                </div>
            </form>
        </div>
    </div>
    @endif
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('showToastrModalAddNote', (event) => {
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
