<div>
@if($showSalute)
    <div class="good-morning-blk-v2" data-duration="{{ $duration }}">
        <div class="row"
            style="background-image: url('{{ $backgroundImage }}');
                    background-size: cover;
                    background-repeat: no-repeat;
                    background-position: center;">
            <div class="col-md-6">
                <div class="morning-user-v2">
                    <h2>
                        <span class="smooth-text-reveal text-white">{{ $greetingMessage }}, {{ $userName }}</span>
                    </h2>
                    {{--}}
                    <p>
                        <span class="smooth-text-reveal text-white">{{ $welcomeMessage }}</span>
                    </p>
                    {{--}}

                    <!-- Loading Spinner -->
                    <div class="mt-4 loading-dashboard" style="display: flex; align-items: center; gap: 12px;">
                        <div class="spinner-border text-white" role="status" style="width: 1.5rem; height: 1.5rem;">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <span class="smooth-text-reveal text-white" style="font-size: 0.95rem;">
                            Espere un momento, estamos preparando su dashboard...
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
</div>
<!-- Script para eliminar el parámetro show_salute de la URL -->
<script>
    // Esperar a que el DOM esté completamente cargado
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initURLCleanup);
    } else {
        initURLCleanup();
    }

    function initURLCleanup() {
        // Pequeño delay para asegurar que Livewire esté listo
        setTimeout(() => {
            if (window.location.href.includes('show_salute=true')) {
                try {
                    const url = new URL(window.location.href);
                    url.searchParams.delete('show_salute');
                    window.history.replaceState({}, '', url);
                    console.log('✅ URL cleaned successfully');
                } catch (error) {
                    console.error('❌ Error cleaning URL:', error);
                }
            }
        }, 500);
    }
</script>
