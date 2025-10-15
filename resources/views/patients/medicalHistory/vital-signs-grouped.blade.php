@php
    $chartData = $this->getVitalSignsChartData();
@endphp

<div class="vital-signs-content" x-data="createVitalSignsCharts(@js($chartData), @js($vitalSignsChartPeriod))" x-init="init()">

    <!-- Header -->
    <div style="margin-bottom: 25px;">
        <h3 style="margin: 0; color: #1e293b; font-size: 20px; font-weight: 600;">
            📊 Evolución de Signos Vitales (Últimos 5 años)
        </h3>
        <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">
            Visualización de la evolución de los signos vitales del paciente
        </p>
    </div>

    <!-- Charts Section -->
    @if(!empty($chartData['bloodPressure']['dates']) || !empty($chartData['heartRate']['dates']) || !empty($chartData['respiratoryRate']['dates']))

        <!-- Loading Skeleton -->
        <div x-show="!chartsLoaded" x-cloak style="opacity: 0.6;">
            @if(!empty($chartData['bloodPressure']['dates']))
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
                <div style="height: 20px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: loading 1.5s infinite; border-radius: 4px; width: 300px; margin-bottom: 20px;"></div>
                <div style="height: 350px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: loading 1.5s infinite; border-radius: 8px;"></div>
            </div>
            @endif

            @if(!empty($chartData['heartRate']['dates']))
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
                <div style="height: 20px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: loading 1.5s infinite; border-radius: 4px; width: 300px; margin-bottom: 20px;"></div>
                <div style="height: 350px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: loading 1.5s infinite; border-radius: 8px;"></div>
            </div>
            @endif

            @if(!empty($chartData['respiratoryRate']['dates']))
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
                <div style="height: 20px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: loading 1.5s infinite; border-radius: 4px; width: 300px; margin-bottom: 20px;"></div>
                <div style="height: 350px; background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%); background-size: 200% 100%; animation: loading 1.5s infinite; border-radius: 8px;"></div>
            </div>
            @endif
        </div>

        <!-- Actual Charts (Hidden until loaded) -->
        <div x-show="chartsLoaded" style="display: none;" wire:ignore>
            <!-- Blood Pressure Chart -->
            @if(!empty($chartData['bloodPressure']['dates']))
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;"
                 id="bp-chart-container-{{ $vitalSignsChartPeriod }}">
                <h4 style="margin: 0 0 20px 0; color: #dc2626; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 20px;">💓</span>
                    Evolución de la Presión Arterial
                </h4>
                <div id="bloodPressureChart-{{ $vitalSignsChartPeriod }}" wire:ignore></div>
            </div>
            @endif

            <!-- Heart Rate Chart -->
            @if(!empty($chartData['heartRate']['dates']))
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;"
                 id="hr-chart-container-{{ $vitalSignsChartPeriod }}">
                <h4 style="margin: 0 0 20px 0; color: #dc2626; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 20px;">❤️</span>
                    Evolución de la Frecuencia Cardíaca
                </h4>
                <div id="heartRateChart-{{ $vitalSignsChartPeriod }}" wire:ignore></div>
            </div>
            @endif

            <!-- Respiratory Rate Chart -->
            @if(!empty($chartData['respiratoryRate']['dates']))
            <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;"
                 id="rr-chart-container-{{ $vitalSignsChartPeriod }}">
                <h4 style="margin: 0 0 20px 0; color: #3b82f6; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 20px;">🫁</span>
                    Evolución de la Frecuencia Respiratoria
                </h4>
                <div id="respiratoryRateChart-{{ $vitalSignsChartPeriod }}" wire:ignore></div>
            </div>
            @endif
        </div>

        <!-- CSS for loading animation -->
        <style>
            @keyframes loading {
                0% { background-position: 200% 0; }
                100% { background-position: -200% 0; }
            }
            [x-cloak] { display: none !important; }
        </style>

    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">📊</div>
            <h4>No hay signos vitales registrados</h4>
            <p>Este paciente no tiene signos vitales en el período seleccionado.</p>
        </div>
    @endif
</div>
