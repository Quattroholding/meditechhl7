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

            <div class="good-morning-blk">
                <div class="row" style= "background-image: url('{{ URL::asset('/assets/img/banner1.png') }}');
                        background-size: cover;
                        background-repeat: no-repeat;
                        background-position: center; "
                        >
                    <div class="col-md-6">
                        <div class="morning-user">
                           <h2>
                               <span class="typewriter-text text-white">{{__('generic.hello')}}, {{auth()->user()->full_name}}</span>
                           </h2>
                           <p>  <span class="typewriter-text text-white">{{__('generic.Have a nice day at work')}}</span></p>

                        </div>
                    </div>
                    {{--}}<div class="col-md-6 position-blk">
                        <div class="morning-img">
                            <img src="{{ URL::asset('/assets/img/morning-img-02.png') }}" alt="">
                        </div>
                    </div>{{--}}
                </div>
            </div>
            <div class="dashboard-initrr">
                <div class="row grid2">
                    @foreach($visibleWidgets as $widget)
                        @if(isset($widgetComponents[$widget['key']]))
                            <div class="grid-item2 grid-item-- {{ $widget['width'] ?? 'col-lg-6' }}" data-order="{{ $widget['order_position'] }}">
                                @livewire($widgetComponents[$widget['key']], ['order' => $widget['order_position']])
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
    <style>
        .grid-item{margin: 5px;}
        .grid-item--col-lg-3 {width:24% }
        .grid-item--col-lg-4 {width:31.33% }
        .grid-item--col-lg-6 {width:48% }
        .grid-item--col-lg-12 {width:99% }
    </style>
    @push('scripts')
        <script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sistema de carga progresiva ultra-simplificado

            $('.grid').masonry({
                // options
                itemSelector: '.grid-item',
            });

            function loadAllWidgets() {
               // console.log('🚀 Starting ultra-simple async widget loading...');

                // Obtener todos los elementos con wire:id
                const allElements = document.querySelectorAll('[wire\\:id]');
                //console.log(`🔍 Found ${allElements.length} Livewire elements`);

                // Crear array de componentes con su order
                const componentsWithOrder = [];

                allElements.forEach((element, index) => {
                    const wireId = element.getAttribute('wire:id');
                    if (wireId) {
                        const component = Livewire.find(wireId);
                        if (component && typeof component.call === 'function') {
                            // Intentar obtener el order del elemento DOM
                            let order = 999; // Default order

                            // Buscar el order en los atributos del elemento o sus padres
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

                //console.log(`📋 Found ${componentsWithOrder.length} components to load`);

                // Cargar cada componente con delay
                componentsWithOrder.forEach((item, index) => {
                    setTimeout(() => {
                        //console.log(`⏰ Loading component ${index + 1}/${componentsWithOrder.length} (order: ${item.order}, wireId: ${item.wireId})`);


                        // Verificar que el método loadData existe antes de llamarlo
                        if (typeof item.component.call !== 'function') {
                            console.error(`❌ Component ${item.wireId} doesn't have call method`);
                            return;
                        }

                        // Verificar si el método loadData existe antes de llamarlo
                        //console.log(`📞 Attempting to call loadData for ${item.wireId}...`);

                        // Check if loadData method exists
                        let hasLoadData = false;
                        try {
                            // Try to access the method through call first
                            hasLoadData = true; // We'll find out in the catch if it doesn't exist
                        } catch(e) {
                            hasLoadData = false;
                        }

                        item.component.call('loadData')
                            .then(() => {
                                //console.log(`✅ Component ${item.wireId} loaded successfully`);
                            })
                            .catch(error => {
                                console.error(`❌ Error loading component ${item.wireId}:`, error);
                                console.error(`❌ Component class:`, item.component.constructor.name);
                                console.error(`❌ Available methods:`, Object.getOwnPropertyNames(Object.getPrototypeOf(item.component)));
                                console.error(`❌ Error type:`, error.constructor.name);
                                console.error(`❌ Error message:`, error.message);

                                // Intentar llamar manualmente si el error es sobre método no encontrado
                                if (error.message && error.message.includes('loadData')) {
                                    console.log(`🔄 Trying direct method call for ${item.wireId}...`);

                                    // Intentar acceso directo al método
                                    if (item.component.data && typeof item.component.data.loadData === 'function') {
                                        console.log(`📞 Found loadData in component.data, calling directly...`);
                                        item.component.data.loadData();
                                    } else if (item.component.loadData && typeof item.component.loadData === 'function') {
                                        console.log(`📞 Found loadData in component, calling directly...`);
                                        item.component.loadData();
                                    } else {
                                        console.error(`❌ loadData method not found anywhere in ${item.wireId}`);
                                    }
                                }
                            });
                    }, index * 500); // 500ms delay entre componentes
                });
            }

            // Esperar a que Livewire esté completamente listo
            function waitForLivewire() {
                if (typeof Livewire !== 'undefined' && Livewire.all) {
                    console.log('✅ Livewire ready');
                    // Esperar un poco más para que todos los componentes estén montados
                    setTimeout(loadAllWidgets, 1000);
                } else {
                    console.log('⏳ Waiting for Livewire...');
                    setTimeout(waitForLivewire, 500);
                }
            }

            // Iniciar el proceso
            waitForLivewire();
        });
    </script>
    @endpush
</x-app-layout>
