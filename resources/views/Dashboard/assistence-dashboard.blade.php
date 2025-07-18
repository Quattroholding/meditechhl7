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
            @endcomponent
            <!-- /Page Header -->

            <div class="good-morning-blk">
                <div class="row">
                    <div class="col-md-6">
                        <div class="morning-user">
                            <h2>
                                <span class="typewriter-text">{{__('generic.hello')}}, {{auth()->user()->full_name}}</span>
                            </h2>
                            <p>  <span class="typewriter-text">{{__('generic.Have a nice day at work')}}</span></p>

                        </div>
                    </div>
                    <div class="col-md-6 position-blk">
                        <div class="morning-img">
                            <img src="{{ URL::asset('/assets/img/morning-img-02.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="dashboard-initrr">
                {{--}}
                <div class="row">
                    <div class="col-lg-6">
                        <div data-order="1">@livewire('doctor.recent-appointment-list',['order'=>1])</div>
                        <div data-order="6">@livewire('doctor.top-active-conditions',['order'=>6])</div>
                        <div data-order="7">@livewire('doctor.top-prescribed-medications',['order'=>7])</div>
                        <div data-order="8">@livewire('doctor.consultation-effectiveness',['order'=>8])</div>
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-5">
                                <div class="col-md-12" data-order="2">@livewire('doctor.new-patients',['order'=>2])</div>
                                <div class="col-md-12" data-order="3">@livewire('doctor.old-patients',['order'=>3])</div>
                                <div class="col-md-12" data-order="4">@livewire('doctor.active-patients',['order'=>4])</div>
                            </div>
                            <div class="col-lg-7">
                                <div class="col-md-12" data-order="5">@livewire('doctor.patients-by-gender',['order'=>5])</div>
                            </div>
                        </div>
                        <div data-order="9">@livewire('doctor.activity-heatmap',['order'=>9])</div>
                    </div>
                </div>
                {{--}}
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Sistema de carga progresiva ultra-simplificado

                function loadAllWidgets() {
                    console.log('🚀 Starting ultra-simple async widget loading...');

                    // Obtener todos los elementos con wire:id
                    const allElements = document.querySelectorAll('[wire\\:id]');
                    console.log(`🔍 Found ${allElements.length} Livewire elements`);

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
                                while (currentElement && order === 999) {
                                    if (currentElement.getAttribute && currentElement.getAttribute('data-order')) {
                                        order = parseInt(currentElement.getAttribute('data-order'));
                                    }
                                    currentElement = currentElement.parentElement;
                                }

                                // Si no se encuentra order, usar un orden secuencial
                                if (order === 999) {
                                    order = index + 1;
                                }

                                componentsWithOrder.push({
                                    component: component,
                                    wireId: wireId,
                                    order: order,
                                    element: element
                                });
                            }
                        }
                    });

                    // Ordenar por order
                    componentsWithOrder.sort((a, b) => a.order - b.order);

                    console.log(`📋 Found ${componentsWithOrder.length} components to load`);

                    // Cargar cada componente con delay
                    componentsWithOrder.forEach((item, index) => {
                        setTimeout(() => {
                            console.log(`⏰ Loading component ${index + 1}/${componentsWithOrder.length} (order: ${item.order}, wireId: ${item.wireId})`);

                            // Debug específico para patients-by-gender
                            if (item.wireId && item.wireId.includes('patients-by-gender')) {
                                console.log(`🔍 DEBUGGING patients-by-gender component:`, item.component);
                                console.log(`🔍 Component methods:`, Object.getOwnPropertyNames(item.component));
                                console.log(`🔍 Component prototype:`, Object.getOwnPropertyNames(Object.getPrototypeOf(item.component)));
                            }

                            // Verificar que el método loadData existe antes de llamarlo
                            if (typeof item.component.call !== 'function') {
                                console.error(`❌ Component ${item.wireId} doesn't have call method`);
                                return;
                            }

                            // Intentar llamar loadData con verificación adicional
                            console.log(`📞 Attempting to call loadData for ${item.wireId}...`);

                            item.component.call('loadData')
                                .then(() => {
                                    console.log(`✅ Component ${item.wireId} loaded successfully`);
                                })
                                .catch(error => {
                                    console.error(`❌ Error loading component ${item.wireId}:`, error);
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
