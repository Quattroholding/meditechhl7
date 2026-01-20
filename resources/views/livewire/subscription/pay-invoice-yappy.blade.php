<div>
    <!-- BOTÓN YAPPY -->
    <btn-yappy
        id="btn-yappy-{{ $invoice->id }}"
        theme="blue">
    </btn-yappy>

    <!-- CLICK DEL BOTÓN (POR FILA) -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            const btn = document.getElementById('btn-yappy-{{ $invoice->id }}');
            if (!btn) return;

            btn.addEventListener('eventClick', () => {
                console.log('Click Yappy factura {{ $invoice->id }}');
                @this.call('crearOrdenYappy');
            });
        });
    </script>

    <!-- LISTENERS GLOBALES (UNA SOLA VEZ) -->
    @once
        <script type="module" src="{{ env('YAPPY_BTN_CDN') }}"></script>

        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('showToastrYappy', (event) => {
                    toastr[event.type](event.message, '', {
                        closeButton: true,
                        progressBar: true,
                        positionClass: 'toast-top-right',
                        timeOut: 5000,
                    });
                });

            });
        </script>
    @endonce
</div>
