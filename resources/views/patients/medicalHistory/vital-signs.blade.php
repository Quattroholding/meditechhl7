<div class="vital-signs-content">
    @php
        $chartData = $this->getVitalSignsChartData();
    @endphp

    <!-- Filter Toggle -->
    <div style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="margin: 0; color: #1e293b; font-size: 20px; font-weight: 600;">
            📊 Evolución de Signos Vitales
        </h3>

        <div style="display: flex; gap: 10px; align-items: center;">
            <label style="font-size: 14px; color: #64748b; font-weight: 500;">Período:</label>
            <select wire:model.live="vitalSignsChartPeriod"
                    style="padding: 8px 16px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 14px; outline: none; cursor: pointer; background: white;">
                <option value="last_year">Último año</option>
                <option value="all">Histórico completo</option>
            </select>
        </div>
    </div>

    @if(!empty($chartData['bloodPressure']['dates']) || !empty($chartData['heartRate']['dates']) || !empty($chartData['respiratoryRate']['dates']))

        <!-- Blood Pressure Chart -->
        @if(!empty($chartData['bloodPressure']['dates']))
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
            <h4 style="margin: 0 0 20px 0; color: #dc2626; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 20px;">💓</span>
                Evolución de la Presión Arterial
            </h4>
            <div id="bloodPressureChart"></div>
        </div>
        @endif

        <!-- Heart Rate Chart -->
        @if(!empty($chartData['heartRate']['dates']))
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
            <h4 style="margin: 0 0 20px 0; color: #dc2626; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 20px;">❤️</span>
                Evolución de la Frecuencia Cardíaca
            </h4>
            <div id="heartRateChart"></div>
        </div>
        @endif

        <!-- Respiratory Rate Chart -->
        @if(!empty($chartData['respiratoryRate']['dates']))
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
            <h4 style="margin: 0 0 20px 0; color: #3b82f6; font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 20px;">🫁</span>
                Evolución de la Frecuencia Respiratoria
            </h4>
            <div id="respiratoryRateChart"></div>
        </div>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                renderVitalSignsCharts();
            });

            // Re-render charts when Livewire updates
            Livewire.hook('morph.updated', ({ el, component }) => {
                renderVitalSignsCharts();
            });

            function renderVitalSignsCharts() {
                const chartData = @json($chartData);

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
                                text: 'Fecha'
                            },
                            labels: {
                                formatter: function(value) {
                                    const date = new Date(value);
                                    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
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
                            horizontalAlign: 'right'
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

                    // Clear and render
                    document.getElementById('bloodPressureChart').innerHTML = '';
                    const bloodPressureChart = new ApexCharts(document.querySelector("#bloodPressureChart"), bloodPressureOptions);
                    bloodPressureChart.render();
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
                                text: 'Fecha'
                            },
                            labels: {
                                formatter: function(value) {
                                    const date = new Date(value);
                                    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
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

                    document.getElementById('heartRateChart').innerHTML = '';
                    const heartRateChart = new ApexCharts(document.querySelector("#heartRateChart"), heartRateOptions);
                    heartRateChart.render();
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
                                text: 'Fecha'
                            },
                            labels: {
                                formatter: function(value) {
                                    const date = new Date(value);
                                    return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short' });
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

                    document.getElementById('respiratoryRateChart').innerHTML = '';
                    const respiratoryRateChart = new ApexCharts(document.querySelector("#respiratoryRateChart"), respiratoryRateOptions);
                    respiratoryRateChart.render();
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