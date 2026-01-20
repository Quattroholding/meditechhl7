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
            document.addEventListener('livewire:init', () => {

                console.log('Yappy listeners cargados');

                Livewire.on('yappy-ready', ([data]) => {

                    console.log('yappy-ready', data);

                    const btn = document.getElementById(`btn-yappy-${data.invoiceId}`);

                    if (!btn) {
                        console.error('Botón Yappy no encontrado');
                        return;
                    }

                    btn.eventPayment({
                        transactionId: data.transactionId,
                        token: data.token,
                        documentName: data.documentName,
                    });
                });

                Livewire.on('yappy-error', ([data]) => {
                    alert(data.message);
                });

            });
        </script>
    @endonce
</div>
