<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
            <i class="fas fa-calendar"></i> {{ __('doctor.dashboard.appointments_of_year', ['year' => date('Y')]) }}
        </h4>
        <p class="text-muted small" style="color: #fff !important;">{{ __('doctor.dashboard.appointments_from_january_to', ['month' => now()->translatedFormat('F')]) }}</p>
    </div>

    <div class="card-body">
        @if($isLoading)
            <div class="loading-skeleton">
                <div class="skeleton-chart-container">
                    <!-- Leyenda skeleton -->
                    <div class="skeleton-legend">
                        @for ($i = 0; $i < 3; $i++)
                            <div class="skeleton-legend-item">
                                <div class="skeleton-legend-box"></div>
                                <div class="skeleton-legend-text"></div>
                            </div>
                        @endfor
                    </div>

                    <!-- Gráfico de barras agrupadas -->
                    <div class="skeleton-bar-chart">
                        @php
                            $monthCount = now()->month;
                        @endphp
                        @for ($i = 0; $i < $monthCount; $i++)
                            <div class="skeleton-bar-group">
                                @for ($j = 0; $j < 3; $j++)
                                    <div class="skeleton-bar" style="height: {{ rand(40, 90) }}%;"></div>
                                @endfor
                            </div>
                        @endfor
                    </div>
                    <div class="skeleton-x-axis"></div>
                    <div class="skeleton-labels">
                        @for ($m = 1; $m <= now()->month; $m++)
                            <span>{{ \Carbon\Carbon::create(now()->year, $m, 1)->format('M') }}</span>
                        @endfor
                    </div>
                </div>
            </div>
        @else
            <div id="yearly-appointments-chart-{{ $order }}"></div>
        @endif
    </div>

    <style>
        .loading-skeleton {
            padding: 10px 0;
        }

        .skeleton-chart-container {
            padding: 20px 10px;
        }

        .skeleton-legend {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-top: 10px;
        }

        .skeleton-legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .skeleton-legend-box {
            width: 8px;
            height: 8px;
            border-radius: 2px;
            background: #008FFB;
            opacity: 0.2;
            animation: boxPulse 1.5s ease-in-out infinite;
        }

        .skeleton-legend-item:nth-child(2) .skeleton-legend-box {
            background: #00E396;
        }

        .skeleton-legend-item:nth-child(3) .skeleton-legend-box {
            background: #FEB019;
        }

        .skeleton-legend-text {
            width: 60px;
            height: 10px;
            background: linear-gradient(90deg, #e5e7eb 0%, #f3f4f6 50%, #e5e7eb 100%);
            background-size: 200% 100%;
            border-radius: 3px;
            animation: shimmer 1.5s ease-in-out infinite;
        }

        .skeleton-bar-chart {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 220px;
            gap: 8px;
            margin-bottom: 5px;
            padding: 0 5px;
        }

        .skeleton-bar-group {
            flex: 1;
            display: flex;
            gap: 2px;
            align-items: flex-end;
            justify-content: center;
        }

        .skeleton-bar {
            width: 30%;
            border-radius: 4px 4px 0 0;
            min-height: 30%;
            animation: barPulse 1.5s ease-in-out infinite;
        }

        .skeleton-bar:nth-child(1) {
            background: #008FFB;
            opacity: 0.15;
            animation-delay: 0s;
        }

        .skeleton-bar:nth-child(2) {
            background: #00E396;
            opacity: 0.15;
            animation-delay: 0.1s;
        }

        .skeleton-bar:nth-child(3) {
            background: #FEB019;
            opacity: 0.15;
            animation-delay: 0.2s;
        }

        .skeleton-x-axis {
            height: 1px;
            background: #dee2e6;
            width: 100%;
            margin-bottom: 8px;
        }

        .skeleton-labels {
            display: flex;
            justify-content: space-between;
            padding: 0 5px;
        }

        .skeleton-labels span {
            font-size: 11px;
            color: #9ca3af;
            opacity: 0.6;
            animation: labelPulse 1.5s ease-in-out infinite;
        }

        @keyframes barPulse {
            0%, 100% { opacity: 0.15; }
            50% { opacity: 0.35; }
        }

        @keyframes labelPulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 0.8; }
        }

        @keyframes boxPulse {
            0%, 100% { opacity: 0.2; }
            50% { opacity: 0.4; }
        }

        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            let chartInstance = null;

            Livewire.on('loadYearlyAppointmentsChart', (event) => {
                const chartData = Array.isArray(event) ? event[0] : event;

                setTimeout(() => {
                    const divElement = document.querySelector("#yearly-appointments-chart-{{ $order }}");

                    if (!divElement) {
                        console.error('❌ Element #yearly-appointments-chart-{{ $order }} not found');
                        return;
                    }

                    if (typeof ApexCharts === 'undefined') {
                        console.error('❌ ApexCharts library not loaded');
                        return;
                    }

                    // Destruir el gráfico existente si existe
                    if (chartInstance) {
                        chartInstance.destroy();
                        chartInstance = null;
                    }

                    // Limpiar el contenedor
                    divElement.innerHTML = '';

                    console.log('✅ Rendering Yearly Appointments by Branch chart', chartData);

                    const options = {
                        series: chartData.series || [],
                        chart: {
                            height: 300,
                            type: 'bar',
                            toolbar: {
                                show: false,
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800,
                            }
                        },
                        colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#3F51B5', '#03A9F4', '#4CAF50'],
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '70%',
                                borderRadius: 4,
                            },
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['transparent']
                        },
                        xaxis: {
                            categories: chartData.categories || [],
                        },
                        yaxis: {
                            title: {
                                text: '{{ __('doctor.dashboard.number_of_appointments') }}'
                            },
                            labels: {
                                formatter: function(value) {
                                    return Math.floor(value);
                                }
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function (val) {
                                    return val + " {{ __('doctor.dashboard.appointments_count') }}"
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'center',
                            fontSize: '12px',
                            fontWeight: 500,
                            markers: {
                                width: 8,
                                height: 8,
                                radius: 2
                            },
                            itemMargin: {
                                horizontal: 8,
                                vertical: 3
                            }
                        },
                        grid: {
                            borderColor: '#e7e7e7',
                            strokeDashArray: 3
                        },
                        noData: {
                            text: '{{ __('doctor.dashboard.no_data_available') }}',
                            align: 'center',
                            verticalAlign: 'middle',
                            style: {
                                fontSize: '14px',
                                color: '#999'
                            }
                        }
                    };

                    try {
                        chartInstance = new ApexCharts(divElement, options);
                        chartInstance.render().then(() => {
                            console.log('✅ ApexCharts rendered successfully for yearly-appointments-chart');
                        }).catch(error => {
                            console.error('❌ Error rendering chart:', error);
                        });
                    } catch (error) {
                        console.error('❌ Error creating ApexCharts:', error);
                    }
                }, 200);
            });
        });
    </script>
</div>
