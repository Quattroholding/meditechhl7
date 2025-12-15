@section('css')
    <!-- Dashboard Animations V2 CSS -->
    <link rel="stylesheet" href="{{ URL::asset('/assets/css/dashboard-animations-v2.css?time='.time()) }}">
    <!-- Dashboard Responsive CSS -->
    <link rel="stylesheet" href="{{ URL::asset('/assets/css/dashboard-responsive.css?time='.time()) }}">
@endsection

<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard
                @endslot
                @slot('li_1')
                    Doctor Dashboard
                @endslot
                @slot('actions')
                    @livewire('widget-configuration', ['dashboardType' => 'doctor'])
                @endslot
            @endcomponent
            <!-- /Page Header -->

            @livewire('welcome-salute')

            <div class="dashboard-init-v2">
                <x-dashboard>
                    @foreach($visibleWidgets as $widget)
                        @if(isset($widgetComponents[$widget['key']]))
                            <x-dashboard-tile
                                :position="$widget['position']"
                                :refresh-interval-in-seconds="120">
                                <div data-order="{{ $widget['order_position'] }}">
                                    @livewire($widgetComponents[$widget['key']], ['order' => $widget['order_position']])
                                </div>
                            </x-dashboard-tile>
                        @endif
                    @endforeach
                </x-dashboard>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
    @section('scripts')
        <script src="{{ URL::asset('/assets/js/dashboard-animations-v2-simple.js?time='.time()) }}"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Sistema de carga progresiva de widgets
                function loadAllWidgets() {
                    console.log('🚀 Starting widget loading...');

                    // Obtener todos los elementos con wire:id
                    const allElements = document.querySelectorAll('[wire\\:id]');
                    console.log(`🔍 Found ${allElements.length} Livewire elements`);

                    // Crear array de componentes con su order
                    const componentsWithOrder = [];

                    allElements.forEach((element) => {
                        const wireId = element.getAttribute('wire:id');
                        if (wireId) {
                            const component = Livewire.find(wireId);
                            if (component && typeof component.call === 'function') {
                                // Intentar obtener el order del elemento DOM
                                let order = 999; // Default order
                                let currentElement = element;
                                let hasOrder = false;

                                while (currentElement && order === 999) {
                                    if (currentElement.getAttribute && currentElement.getAttribute('data-order')) {
                                        order = parseInt(currentElement.getAttribute('data-order'));
                                        hasOrder = true;
                                    }
                                    currentElement = currentElement.parentElement;
                                }

                                // Solo incluir componentes que tienen data-order (widgets del dashboard)
                                if (hasOrder) {
                                    componentsWithOrder.push({
                                        component: component,
                                        wireId: wireId,
                                        order: order,
                                        element: element
                                    });
                                }
                            }
                        }
                    });

                    // Ordenar por order
                    componentsWithOrder.sort((a, b) => a.order - b.order);
                    console.log(`📋 Found ${componentsWithOrder.length} widgets to load`);

                    // Cargar cada componente con delay
                    componentsWithOrder.forEach((item, index) => {
                        setTimeout(() => {
                            console.log(`⏰ Loading widget ${index + 1}/${componentsWithOrder.length} (order: ${item.order})`);

                            item.component.call('loadData')
                                .then(() => {
                                    console.log(`✅ Widget ${item.wireId} loaded successfully`);
                                })
                                .catch(error => {
                                    console.error(`❌ Error loading widget ${item.wireId}:`, error);
                                });
                        }, index * 300); // 300ms delay entre widgets
                    });
                }

                // Esperar a que Livewire esté completamente listo
                function waitForLivewire() {
                    if (typeof Livewire !== 'undefined' && Livewire.all) {
                        console.log('✅ Livewire ready, waiting 2s before loading widgets...');
                        // Esperar 2 segundos para asegurar que la animación termine o casi termine
                        setTimeout(loadAllWidgets, 2000);
                    } else {
                        console.log('⏳ Waiting for Livewire...');
                        setTimeout(waitForLivewire, 500);
                    }
                }

                // Iniciar el proceso
                waitForLivewire();
            });
        </script>
    @endsection
</x-app-layout>


