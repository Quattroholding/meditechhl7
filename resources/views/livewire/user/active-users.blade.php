<button type="button" style="{{ $this->visibility }}" class="btn btn-warning btn-sm" title="{{ __('Reactivar Usuario') }}"
    wire:click.prevent="activateUser({{ $user_id }})" wire:loading.attr="disabled"
    wire:target="activateUser({{ $user_id }})">

    <i class="fa fa-check-circle m-r-5" wire:loading.remove wire:target="activateUser({{ $user_id }})"></i>

    <i class="fa fa-spinner fa-spin m-r-5" wire:loading wire:target="activateUser({{ $user_id }})"></i>

    <span wire:loading.remove wire:target="activateUser({{ $user_id }})">
        {{ __('Activar') }}
    </span>
    <span wire:loading wire:target="activateUser({{ $user_id }})">
        {{ __('Activando...') }}
    </span>
</button>
@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr{{ $user_id }}', (event) => {
                // Verificar que toastr esté disponible
                if (typeof toastr !== 'undefined') {
                    // Acceder al primer elemento del array si event es un array
                    const data = Array.isArray(event) ? event[0] : event;

                    toastr[data.type](data.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000,
                    });
                } else {
                    // Fallback usando alert
                    alert(data.message);
                }
            });
        });
    </script>
@endpush
