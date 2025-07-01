<div class="">
    @if($patient)
        <!-- Header del paciente -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                {{--}}
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $patient->full_name }}</h1>
                    <p class="text-gray-600">
                        {{ $patient->identifier_type }}: {{ $patient->identifier }} •
                        @if($patient->age) {{ $patient->age }} años • @endif
                        {{ ucfirst(__('patient.'.$patient->gender) ?? 'No especificado') }}
                    </p>
                </div>
                {{--}}

                <!-- Controles -->
                <div class="flex items-center space-x-4">
                    <select wire:model.live="selectedPeriod"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="1">Último día</option>
                        <option value="7">Última semana</option>
                        <option value="30">Último mes</option>
                        <option value="90">Últimos 3 meses</option>
                    </select>

                    <button wire:click="refreshData"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Actualizar
                    </button>
                </div>
            </div>
        </div>

        <!-- Estado General de Salud -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center justify-center w-16 h-16 rounded-full
                        @if($overallStatus['color'] === 'green') bg-green-100
                        @elseif($overallStatus['color'] === 'yellow') bg-yellow-100
                        @elseif($overallStatus['color'] === 'orange') bg-orange-100
                        @elseif($overallStatus['color'] === 'red') bg-red-100
                        @else bg-gray-100
                        @endif">
                        <span class="text-2xl">
                            @if($overallStatus['status'] === 'normal') ✅
                            @elseif($overallStatus['status'] === 'caution') ⚠️
                            @elseif($overallStatus['status'] === 'warning') ⚠️
                            @elseif($overallStatus['status'] === 'critical') 🚨
                            @else ❓
                            @endif
                        </span>
                    </div>

                    <div>
                        <h2 class="text-xl font-semibold
                            @if($overallStatus['color'] === 'green') text-green-800
                            @elseif($overallStatus['color'] === 'yellow') text-yellow-800
                            @elseif($overallStatus['color'] === 'orange') text-orange-800
                            @elseif($overallStatus['color'] === 'red') text-red-800
                            @else text-gray-800
                            @endif">
                            Estado General: {{ ucfirst($overallStatus['status']) }}
                        </h2>
                        <p class="text-gray-600">{{ $overallStatus['message'] }}</p>
                    </div>
                </div>

                <!-- Score de salud -->
                <div class="text-right">
                    <div class="text-3xl font-bold
                        @if($overallStatus['color'] === 'green') text-green-600
                        @elseif($overallStatus['color'] === 'yellow') text-yellow-600
                        @elseif($overallStatus['color'] === 'orange') text-orange-600
                        @elseif($overallStatus['color'] === 'red') text-red-600
                        @else text-gray-600
                        @endif">
                        {{ number_format($overallStatus['score'], 0) }}%
                    </div>
                    <p class="text-sm text-gray-500">Score de Salud</p>
                </div>
            </div>

            <!-- Barra de progreso -->
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500
                        @if($overallStatus['color'] === 'green') bg-green-500
                        @elseif($overallStatus['color'] === 'yellow') bg-yellow-500
                        @elseif($overallStatus['color'] === 'orange') bg-orange-500
                        @elseif($overallStatus['color'] === 'red') bg-red-500
                        @else bg-gray-500
                        @endif"
                         style="width: {{ $overallStatus['score'] }}%">
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Signos Vitales -->
        @if($vitalSigns->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($vitalSigns as $vitalSign)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                        <!-- Header de la tarjeta -->
                        <div class="p-6 pb-4">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl">{{ $vitalSign['config']['icon'] }}</span>
                                    <div>
                                        <h3 class="font-semibold text-gray-900">{{ $vitalSign['config']['name'] }}</h3>
                                        <p class="text-xs text-gray-500">
                                            {{ $vitalSign['last_updated']->format('d/m/Y H:i') }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Indicador de tendencia -->
                                <div class="flex items-center space-x-1">
                                    @if($vitalSign['trend'] === 'increasing')
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z"/>
                                        </svg>
                                    @elseif($vitalSign['trend'] === 'decreasing')
                                        <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M14.707 12.293a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l2.293-2.293a1 1 0 011.414 0z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M3 10h14M3 10l4-4m-4 4l4 4"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Valor principal -->
                        <div class="px-6 pb-4">
                            <div class="text-center">
                                <div class="text-3xl font-bold
                                    @if($vitalSign['risk_level'] === 'normal') text-green-600
                                    @elseif($vitalSign['risk_level'] === 'low' || $vitalSign['risk_level'] === 'high') text-yellow-600
                                    @elseif($vitalSign['risk_level'] === 'critical') text-red-600
                                    @else text-gray-600
                                    @endif">
                                    {{ $vitalSign['value']['display'] }}
                                </div>
                            </div>
                        </div>

                        <!-- Estado y rango -->
                        <div class="border-t border-gray-200 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($vitalSign['risk_level'] === 'normal') bg-green-100 text-green-800
                                    @elseif($vitalSign['risk_level'] === 'low') bg-yellow-100 text-yellow-800
                                    @elseif($vitalSign['risk_level'] === 'high') bg-yellow-100 text-yellow-800
                                    @elseif($vitalSign['risk_level'] === 'critical') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ $vitalSign['status'] }}
                                </span>

                                <!-- Rango normal -->

                                @if($vitalSign['config']['normal_range'] && $vitalSign['config']['loinc_code'] !== '85354-9')
                                    <span class="text-xs text-gray-500">
                                        Normal: {{$vitalSign['config']['normal_range'][0]}} - {{$vitalSign['config']['normal_range'][1]}} {{ $vitalSign['config']['unit'] }}
                                    </span>
                                @elseif($vitalSign['config']['loinc_code'] === '85354-9')
                                    <span class="text-xs text-gray-500">
                                        Normal: &lt;140/90 mmHg
                                    </span>
                                @endif

                            </div>

                            <!-- Información adicional para presión arterial -->
                            @if($vitalSign['config']['loinc_code'] === '85354-9' && isset($vitalSign['value']['systolic']))
                                <div class="mt-3 space-y-1">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Sistólica:</span>
                                        <span class="font-medium">{{ $vitalSign['value']['systolic'] }} mmHg</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Diastólica:</span>
                                        <span class="font-medium">{{ $vitalSign['value']['diastolic'] }} mmHg</span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Acciones -->
                        <div class="bg-gray-50 px-6 py-3 rounded-b-lg">
                            <button class="w-full text-center text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors duration-200">
                                Ver historial
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Resumen de alertas -->
            @php
                $criticalSigns = $vitalSigns->where('risk_level', 'critical');
                $abnormalSigns = $vitalSigns->whereIn('risk_level', ['high', 'low']);
            @endphp

            @if($criticalSigns->count() > 0 || $abnormalSigns->count() > 0)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">🚨 Alertas de Salud</h3>

                    @if($criticalSigns->count() > 0)
                        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <h4 class="font-medium text-red-800 mb-2">Valores Críticos - Requiere Atención Inmediata</h4>
                            <ul class="space-y-1">
                                @foreach($criticalSigns as $sign)
                                    <li class="text-sm text-red-700">
                                        • {{ $sign['config']['name'] }}: {{ $sign['value']['display'] }} ({{ $sign['status'] }})
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($abnormalSigns->count() > 0)
                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <h4 class="font-medium text-yellow-800 mb-2">Valores Fuera del Rango Normal</h4>
                            <ul class="space-y-1">
                                @foreach($abnormalSigns as $sign)
                                    <li class="text-sm text-yellow-700">
                                        • {{ $sign['config']['name'] }}: {{ $sign['value']['display'] }} ({{ $sign['status'] }})
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-4 flex justify-end space-x-3">
                        <button class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            Contactar Médico
                        </button>
                        <button class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            Programar Cita
                        </button>
                    </div>
                </div>
            @endif
        @else
            <!-- Estado sin datos -->
            <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No hay signos vitales registrados</h3>
                <p class="text-gray-500 mb-4">
                    No se encontraron mediciones de signos vitales en el período seleccionado.
                </p>
                <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Registrar Signos Vitales
                </button>
            </div>
        @endif

    @else
        <!-- Error: Paciente no encontrado -->
        <div class="text-center py-12 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="mx-auto h-12 w-12 text-red-400 mb-4">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Paciente no encontrado</h3>
            <p class="text-gray-500">No se pudo cargar la información del paciente.</p>
        </div>
    @endif

    <!-- Loading state -->
    <div wire:loading.delay wire:target="refreshData,selectedPeriod"
         class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Actualizando signos vitales...</span>
            </div>
        </div>
    </div>
</div>

<!-- Auto refresh cada 30 segundos si está habilitado -->
@if($autoRefresh)
    <script>
        document.addEventListener('livewire:initialized', () => {
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                @this.call('refreshData');
                }
            }, 30000); // 30 segundos
        });
    </script>
@endif
