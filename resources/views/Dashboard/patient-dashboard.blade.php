<?php $page = 'patient-dashboard'; ?>
<x-app-layout>

    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard
                @endslot
                @slot('li_1')
                    {{__('patient.title')}}
                @endslot
                @slot('actions')
                    @livewire('widget-configuration', ['dashboardType' => 'patient'])
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="good-morning-blk">
                <div class="row">
                    <div class="col-md-6">
                        <div class="morning-user">
                            <h2><span class="typewriter-text">{{__('generic.hello')}} , {{auth()->user()->patient->name}}</span></h2>
                            <p><span class="typewriter-text">{{__('generic.welcome')}}</span></p>
                        </div>
                    </div>
                    <div class="col-md-6 position-blk">
                        <div class="morning-img">
                            <img src="{{ URL::asset('/assets/img/morning-img-21.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            @include('partials.message')
            <div class="dashboard-initrr">
                <div class="row">
                    @foreach($visibleWidgets as $widget)
                        @if(isset($widgetComponents[$widget['key']]))
                            <div class="{{ $widget['width'] ?? 'col-lg-6' }}" data-order="{{ $widget['order_position'] }}">
                                @if($widget['key'] === 'upcoming-appointments' || $widget['key'] === 'recent-consultations' || $widget['key'] === 'outstanding-invoices')
                                    @livewire($widgetComponents[$widget['key']], ['limit' => 3, 'order' => $widget['order_position']])
                                @else
                                    @livewire($widgetComponents[$widget['key']], ['order' => $widget['order_position']])
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sistema de carga progresiva para patient dashboard

            function loadAllWidgets() {
                console.log('🚀 Starting async widget loading for patient dashboard...');

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

                console.log(`📋 Found ${componentsWithOrder.length} components to load`);

                // Cargar cada componente con delay
                componentsWithOrder.forEach((item, index) => {
                    setTimeout(() => {
                        console.log(`⏰ Loading component ${index + 1}/${componentsWithOrder.length} (order: ${item.order}, wireId: ${item.wireId})`);

                        // Verificar que el método loadData existe antes de llamarlo
                        if (typeof item.component.call !== 'function') {
                            console.error(`❌ Component ${item.wireId} doesn't have call method`);
                            return;
                        }

                        // Usar eventos de Livewire para mayor compatibilidad
                        console.log(`📞 Dispatching loadData event for ${item.wireId}...`);

                        try {
                            // Método preferido: Usar dispatch de eventos
                            const freshComponent = Livewire.find(item.wireId);
                            if (freshComponent) {
                                // Intentar dispatch primero
                                try {
                                    freshComponent.dispatch('loadData');
                                    console.log(`✅ Event dispatched successfully for ${item.wireId}`);
                                } catch (dispatchError) {
                                    console.log(`📞 Dispatch failed, trying direct call for ${item.wireId}...`);
                                    // Fallback a llamada directa
                                    if (typeof freshComponent.call === 'function') {
                                        freshComponent.call('loadData')
                                            .then(() => {
                                                console.log(`✅ Component ${item.wireId} loaded successfully via call`);
                                            })
                                            .catch(callError => {
                                                console.error(`❌ Both dispatch and call failed for ${item.wireId}:`, callError);
                                            });
                                    } else {
                                        console.error(`❌ Component ${item.wireId} has no call method`);
                                    }
                                }
                            } else {
                                console.error(`❌ Component ${item.wireId} not found`);
                            }
                        } catch (error) {
                            console.error(`❌ Exception loading component ${item.wireId}:`, error);
                        }
                    }, index * 500); // 500ms delay entre componentes
                });
            }

            // Esperar a que Livewire esté completamente listo
            function waitForLivewire() {
                if (typeof Livewire !== 'undefined' && Livewire.all) {
                    console.log('✅ Livewire ready for patient dashboard');
                    // Esperar un poco más para que todos los componentes estén montados
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
    @endpush
</x-app-layout>
