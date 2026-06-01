@php
    $chartData = $this->getVitalSignsChartData();
@endphp

<div class="vital-signs-content" x-data="createVitalSignsCharts(@js($chartData), @js($vitalSignsChartPeriod))" x-init="init()">

    <!-- Header -->
    <div style="margin-bottom: 25px;">
        <h3 style="margin: 0; color: #1e293b; font-size: 20px; font-weight: 600;">
            📊 {{ __('patient.medical_history.vital_signs_grouped.vital_signs_evolution') }} ({{ __('patient.medical_history.vital_signs_grouped.last_5_years') }})
        </h3>
        <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">
            {{ __('patient.medical_history.vital_signs_grouped.patient_evolution_visualization') }}
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
                    {{ __('patient.medical_history.vital_signs_grouped.blood_pressure_evolution') }}
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
                    {{ __('patient.medical_history.vital_signs_grouped.heart_rate_evolution') }}
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
                    {{ __('patient.medical_history.vital_signs_grouped.respiratory_rate_evolution') }}
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
            <h4>{{ __('patient.medical_history.vital_signs_grouped.no_vital_signs') }}</h4>
            <p>{{ __('patient.medical_history.vital_signs_grouped.no_vital_signs_message') }}</p>
        </div>
    @endif
</div>
