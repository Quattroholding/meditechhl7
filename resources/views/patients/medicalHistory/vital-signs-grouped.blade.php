<div class="vital-signs-content">
    @php
        $chartData = $this->getVitalSignsChartData();
    @endphp

    <!-- Charts Section -->
    @if(!empty($chartData['bloodPressure']['dates']) || !empty($chartData['heartRate']['dates']) || !empty($chartData['respiratoryRate']['dates']))
        <!-- Header -->
        <div style="margin-bottom: 25px;">
            <h3 style="margin: 0; color: #1e293b; font-size: 20px; font-weight: 600;">
                📊 Evolución de Signos Vitales (Últimos 5 años)
            </h3>
            <p style="margin: 5px 0 0 0; color: #64748b; font-size: 14px;">
                Visualización de la evolución de los signos vitales del paciente
            </p>
        </div>

        <!-- Blood Pressure Chart -->
        @if(!empty($chartData['bloodPressure']['dates']))
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;"
             wire:key="bp-chart-{{ $vitalSignsChartPeriod }}">
            <h4 style="margin: 0 0 20px 0; color: #dc2626; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 20px;">💓</span>
                Evolución de la Presión Arterial
            </h4>
            <div id="bloodPressureChart-{{ $vitalSignsChartPeriod }}"></div>
        </div>
        @endif

        <!-- Heart Rate Chart -->
        @if(!empty($chartData['heartRate']['dates']))
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;"
             wire:key="hr-chart-{{ $vitalSignsChartPeriod }}">
            <h4 style="margin: 0 0 20px 0; color: #dc2626; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 20px;">❤️</span>
                Evolución de la Frecuencia Cardíaca
            </h4>
            <div id="heartRateChart-{{ $vitalSignsChartPeriod }}"></div>
        </div>
        @endif

        <!-- Respiratory Rate Chart -->
        @if(!empty($chartData['respiratoryRate']['dates']))
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;"
             wire:key="rr-chart-{{ $vitalSignsChartPeriod }}">
            <h4 style="margin: 0 0 20px 0; color: #3b82f6; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 20px;">🫁</span>
                Evolución de la Frecuencia Respiratoria
            </h4>
            <div id="respiratoryRateChart-{{ $vitalSignsChartPeriod }}"></div>
        </div>
        @endif

        <!-- Charts JavaScript -->
        <script wire:key="chart-script-{{ $vitalSignsChartPeriod }}">
            // Store chart instances globally to destroy them before re-rendering
            let bloodPressureChartInstance = null;
            let heartRateChartInstance = null;
            let respiratoryRateChartInstance = null;

            // Wait for DOM to be fully loaded
            setTimeout(() => {
                renderVitalSignsCharts();
            }, 500);

            function renderVitalSignsCharts() {
                const chartData = @json($chartData);

                console.log('Chart data:', chartData);

                // Destroy existing chart instances
                if (bloodPressureChartInstance) {
                    bloodPressureChartInstance.destroy();
                    bloodPressureChartInstance = null;
                }
                if (heartRateChartInstance) {
                    heartRateChartInstance.destroy();
                    heartRateChartInstance = null;
                }
                if (respiratoryRateChartInstance) {
                    respiratoryRateChartInstance.destroy();
                    respiratoryRateChartInstance = null;
                }

                // Blood Pressure Chart
                if (chartData.bloodPressure && chartData.bloodPressure.dates.length > 0) {
                    const bloodPressureOptions = {
                        series: [{
                            name: 'Sistólica',
                            data: chartData.bloodPressure.systolic,
                            color: '#dc2626'
                        }, {
                            name: 'Diastólica',
                            data: chartData.bloodPressure.diastolic,
                            color: '#f59e0b'
                        }],
                        chart: {
                            height: 350,
                            type: 'line',
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    zoom: true,
                                    zoomin: true,
                                    zoomout: true,
                                    pan: true,
                                    reset: true
                                }
                            },
                            zoom: {
                                enabled: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        title: {
                            text: undefined
                        },
                        grid: {
                            borderColor: '#f1f5f9',
                            row: {
                                colors: ['#f8fafc', 'transparent'],
                                opacity: 0.5
                            },
                        },
                        markers: {
                            size: 5,
                            hover: {
                                size: 7
                            }
                        },
                        xaxis: {
                            categories: chartData.bloodPressure.dates,
                            title: {
                                text: 'Fecha de Consulta'
                            },
                            labels: {
                                formatter: function(value) {
                                    const date = new Date(value);
                                    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Presión (mmHg)'
                            },
                            min: 40,
                            max: 200
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'left'
                        },
                        tooltip: {
                            x: {
                                formatter: function(value) {
                                    const date = new Date(value);
                                    return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
                                }
                            },
                            y: {
                                formatter: function(value) {
                                    return value + ' mmHg';
                                }
                            }
                        }
                    };

                    // Clear and render with dynamic ID
                    const bpId = 'bloodPressureChart-{{ $vitalSignsChartPeriod }}';
                    const bpEl = document.getElementById(bpId);
                    if (bpEl) {
                        bpEl.innerHTML = '';
                        bloodPressureChartInstance = new ApexCharts(bpEl, bloodPressureOptions);
                        bloodPressureChartInstance.render();
                    }
                }

                // Heart Rate Chart
                if (chartData.heartRate && chartData.heartRate.dates.length > 0) {
                    const heartRateOptions = {
                        series: [{
                            name: 'Frecuencia Cardíaca',
                            data: chartData.heartRate.values,
                            color: '#dc2626'
                        }],
                        chart: {
                            height: 350,
                            type: 'line',
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    zoom: true,
                                    zoomin: true,
                                    zoomout: true,
                                    pan: true,
                                    reset: true
                                }
                            },
                            zoom: {
                                enabled: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        title: {
                            text: undefined
                        },
                        grid: {
                            borderColor: '#f1f5f9',
                            row: {
                                colors: ['#fef2f2', 'transparent'],
                                opacity: 0.5
                            },
                        },
                        markers: {
                            size: 5,
                            hover: {
                                size: 7
                            }
                        },
                        xaxis: {
                            categories: chartData.heartRate.dates,
                            title: {
                                text: 'Fecha de Consulta'
                            },
                            labels: {
                                formatter: function(value) {
                                    const date = new Date(value);
                                    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Frecuencia (bpm)'
                            },
                            min: 40,
                            max: 160
                        },
                        tooltip: {
                            x: {
                                formatter: function(value) {
                                    const date = new Date(value);
                                    return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
                                }
                            },
                            y: {
                                formatter: function(value) {
                                    return value + ' bpm';
                                }
                            }
                        }
                    };

                    const hrId = 'heartRateChart-{{ $vitalSignsChartPeriod }}';
                    const hrEl = document.getElementById(hrId);
                    if (hrEl) {
                        hrEl.innerHTML = '';
                        heartRateChartInstance = new ApexCharts(hrEl, heartRateOptions);
                        heartRateChartInstance.render();
                    }
                }

                // Respiratory Rate Chart
                if (chartData.respiratoryRate && chartData.respiratoryRate.dates.length > 0) {
                    const respiratoryRateOptions = {
                        series: [{
                            name: 'Frecuencia Respiratoria',
                            data: chartData.respiratoryRate.values,
                            color: '#3b82f6'
                        }],
                        chart: {
                            height: 350,
                            type: 'line',
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    zoom: true,
                                    zoomin: true,
                                    zoomout: true,
                                    pan: true,
                                    reset: true
                                }
                            },
                            zoom: {
                                enabled: true
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        title: {
                            text: undefined
                        },
                        grid: {
                            borderColor: '#f1f5f9',
                            row: {
                                colors: ['#eff6ff', 'transparent'],
                                opacity: 0.5
                            },
                        },
                        markers: {
                            size: 5,
                            hover: {
                                size: 7
                            }
                        },
                        xaxis: {
                            categories: chartData.respiratoryRate.dates,
                            title: {
                                text: 'Fecha de Consulta'
                            },
                            labels: {
                                formatter: function(value) {
                                    const date = new Date(value);
                                    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Frecuencia (rpm)'
                            },
                            min: 8,
                            max: 40
                        },
                        tooltip: {
                            x: {
                                formatter: function(value) {
                                    const date = new Date(value);
                                    return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
                                }
                            },
                            y: {
                                formatter: function(value) {
                                    return value + ' rpm';
                                }
                            }
                        }
                    };

                    const rrId = 'respiratoryRateChart-{{ $vitalSignsChartPeriod }}';
                    const rrEl = document.getElementById(rrId);
                    if (rrEl) {
                        rrEl.innerHTML = '';
                        respiratoryRateChartInstance = new ApexCharts(rrEl, respiratoryRateOptions);
                        respiratoryRateChartInstance.render();
                    }
                }
            }
        </script>

    @else
        <div style="text-align: center; padding: 60px; color: #64748b;">
            <div style="font-size: 48px; margin-bottom: 20px;">📊</div>
            <h3>No hay signos vitales registrados</h3>
            <p>Este paciente no tiene signos vitales en el período seleccionado.</p>
        </div>
    @endif
</div>
