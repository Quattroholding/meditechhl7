<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
            <i class="fas fa-chart-line"></i> Facturación por Sede - {{ $year }}
        </h4>
        @if(!$isLoading)
            <div class="d-flex gap-2">
                <select wire:model.live="year" class="form-select form-select-sm" style="width: auto;">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
                <select wire:model.live="selectedBranches" multiple class="form-select form-select-sm" style="width: 200px; height: 38px;">
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <div class="card-body">
        @if($isLoading)
            <div class="loading-skeleton">
                <div class="skeleton-chart-container">
                    <!-- Líneas de gráfico skeleton -->
                    <div class="skeleton-line-chart">
                        <svg width="100%" height="250" viewBox="0 0 600 250">
                            <!-- Líneas horizontales de guía -->
                            <line x1="40" y1="50" x2="580" y2="50" stroke="#e5e7eb" stroke-width="1"/>
                            <line x1="40" y1="100" x2="580" y2="100" stroke="#e5e7eb" stroke-width="1"/>
                            <line x1="40" y1="150" x2="580" y2="150" stroke="#e5e7eb" stroke-width="1"/>
                            <line x1="40" y1="200" x2="580" y2="200" stroke="#e5e7eb" stroke-width="1"/>

                            <!-- Línea 1 (azul) -->
                            <polyline class="skeleton-line skeleton-line-1"
                                      points="50,180 95,120 140,145 185,90 230,110 275,70 320,95 365,60 410,85 455,50 500,75 545,65"
                                      fill="none"
                                      stroke="#008FFB"
                                      stroke-width="3"
                                      opacity="0.15"/>

                            <!-- Línea 2 (verde) -->
                            <polyline class="skeleton-line skeleton-line-2"
                                      points="50,200 95,160 140,175 185,140 230,155 275,120 320,135 365,110 410,125 455,95 500,110 545,100"
                                      fill="none"
                                      stroke="#00E396"
                                      stroke-width="3"
                                      opacity="0.15"/>

                            <!-- Línea 3 (naranja) -->
                            <polyline class="skeleton-line skeleton-line-3"
                                      points="50,190 95,145 140,165 185,125 230,140 275,105 320,120 365,95 410,110 455,80 500,95 545,85"
                                      fill="none"
                                      stroke="#FEB019"
                                      stroke-width="3"
                                      opacity="0.15"/>
                        </svg>
                    </div>

                    <!-- Labels de meses -->
                    <div class="skeleton-labels">
                        <span>Ene</span>
                        <span>Feb</span>
                        <span>Mar</span>
                        <span>Abr</span>
                        <span>May</span>
                        <span>Jun</span>
                        <span>Jul</span>
                        <span>Ago</span>
                        <span>Sep</span>
                        <span>Oct</span>
                        <span>Nov</span>
                        <span>Dic</span>
                    </div>

                    <!-- Leyenda skeleton -->
                    <div class="skeleton-legend">
                        <div class="skeleton-legend-item">
                            <div class="skeleton-legend-box" style="background: #008FFB; opacity: 0.15;"></div>
                            <div class="skeleton-legend-text"></div>
                        </div>
                        <div class="skeleton-legend-item">
                            <div class="skeleton-legend-box" style="background: #00E396; opacity: 0.15;"></div>
                            <div class="skeleton-legend-text"></div>
                        </div>
                        <div class="skeleton-legend-item">
                            <div class="skeleton-legend-box" style="background: #FEB019; opacity: 0.15;"></div>
                            <div class="skeleton-legend-text"></div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div id="branch-billing-chart-{{ $order ?? 'default' }}"></div>
        @endif
    </div>

    <style>
        .loading-skeleton {
            padding: 10px 0;
        }

        .skeleton-chart-container {
            padding: 20px 10px;
        }

        .skeleton-line-chart {
            margin-bottom: 10px;
        }

        .skeleton-line {
            animation: linePulse 2s ease-in-out infinite;
        }

        .skeleton-line-1 {
            animation-delay: 0s;
        }

        .skeleton-line-2 {
            animation-delay: 0.3s;
        }

        .skeleton-line-3 {
            animation-delay: 0.6s;
        }

        .skeleton-labels {
            display: flex;
            justify-content: space-between;
            padding: 0 40px;
            margin-top: 10px;
        }

        .skeleton-labels span {
            font-size: 11px;
            color: #9ca3af;
            opacity: 0.6;
            animation: labelPulse 1.5s ease-in-out infinite;
        }

        .skeleton-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 20px;
        }

        .skeleton-legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .skeleton-legend-box {
            width: 12px;
            height: 12px;
            border-radius: 2px;
            animation: boxPulse 1.5s ease-in-out infinite;
        }

        .skeleton-legend-text {
            width: 80px;
            height: 12px;
            background: linear-gradient(90deg, #e5e7eb 0%, #f3f4f6 50%, #e5e7eb 100%);
            background-size: 200% 100%;
            border-radius: 4px;
            animation: shimmer 1.5s ease-in-out infinite;
        }

        @keyframes linePulse {
            0%, 100% { opacity: 0.15; }
            50% { opacity: 0.4; }
        }

        @keyframes labelPulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 0.8; }
        }

        @keyframes boxPulse {
            0%, 100% { opacity: 0.15; }
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

            Livewire.on('loadBranchBillingChart', (event) => {
                const chartData = Array.isArray(event) ? event[0] : event;

                setTimeout(() => {
                    const divElement = document.querySelector("#branch-billing-chart-{{ $order ?? 'default' }}");

                    if (!divElement) {
                        console.error('❌ Element #branch-billing-chart-{{ $order ?? 'default' }} not found');
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

                    console.log('✅ Rendering chart with data:', chartData);

                    const options = {
                        series: chartData.series || [],
                        chart: {
                            type: 'line',
                            height: 350,
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
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800,
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        stroke: {
                            curve: 'smooth',
                            width: 3
                        },
                        xaxis: {
                            categories: chartData.categories || [],
                            title: {
                                text: 'Mes',
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 600
                                }
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Monto Total ($USD)',
                                style: {
                                    fontSize: '12px',
                                    fontWeight: 600
                                }
                            },
                            labels: {
                                formatter: function(value) {
                                    if (value === null || value === undefined) return '$0.00';
                                    return '$' + value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                                }
                            }
                        },
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: {
                                formatter: function(value) {
                                    if (value === null || value === undefined) return '$0.00';
                                    return '$' + value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                                }
                            }
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'center',
                            floating: false,
                            fontSize: '13px',
                            fontWeight: 500,
                            markers: {
                                width: 10,
                                height: 10,
                                radius: 2
                            },
                            itemMargin: {
                                horizontal: 10,
                                vertical: 5
                            }
                        },
                        grid: {
                            borderColor: '#e7e7e7',
                            strokeDashArray: 3
                        },
                        colors: ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#3F51B5', '#03A9F4', '#4CAF50', '#F9CE1D', '#FF9800'],
                        markers: {
                            size: 4,
                            hover: {
                                size: 6
                            }
                        },
                        noData: {
                            text: 'No hay datos disponibles',
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
                            console.log('✅ Branch Billing Chart rendered successfully');
                        }).catch(error => {
                            console.error('❌ Error rendering chart:', error);
                        });
                    } catch (error) {
                        console.error('❌ Error creating ApexCharts:', error);
                    }
                }, 200);
            });

            // Listen for update events
            Livewire.on('updateChart', (event) => {
                const chartData = Array.isArray(event) ? event[0] : event;
                Livewire.dispatch('loadBranchBillingChart', chartData);
            });
        });
    </script>
</div>
