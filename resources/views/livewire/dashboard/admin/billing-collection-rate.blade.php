<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
            <i class="fas fa-chart-bar"></i> Facturación vs Cobros - {{ $year }}
        </h4>
        @if(!$isLoading)
            <div class="d-flex align-items-center gap-3">
                <!-- Resumen -->
                <div class="d-flex gap-3">
                    <div class="text-end">
                        <small class="text-muted d-block">Facturado</small>
                        <strong class="text-primary">${{ number_format($totalBilling, 2) }}</strong>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Cobrado</small>
                        <strong class="text-success">${{ number_format($totalCollections, 2) }}</strong>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Tasa</small>
                        <strong class="text-info">{{ number_format($averageRate, 1) }}%</strong>
                    </div>
                </div>
                <!-- Selector de año -->
                <select wire:model.live="year" class="form-select form-select-sm" style="width: auto;">
                    @for ($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        @endif
    </div>

    <div class="card-body">
        @if($isLoading)
            <div class="loading-skeleton">
                <div class="skeleton-chart-container">
                    <!-- Gráfico de barras skeleton -->
                    <div class="skeleton-bar-chart">
                        @for ($i = 0; $i < 12; $i++)
                            <div class="skeleton-bar-group">
                                <div class="skeleton-bar skeleton-bar-primary" style="height: {{ rand(40, 90) }}%;"></div>
                                <div class="skeleton-bar skeleton-bar-success" style="height: {{ rand(30, 85) }}%;"></div>
                            </div>
                        @endfor
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
                            <div class="skeleton-legend-box" style="background: #008FFB; opacity: 0.2;"></div>
                            <span>Facturado</span>
                        </div>
                        <div class="skeleton-legend-item">
                            <div class="skeleton-legend-box" style="background: #00E396; opacity: 0.2;"></div>
                            <span>Cobrado</span>
                        </div>
                        <div class="skeleton-legend-item">
                            <div class="skeleton-legend-box" style="background: #FEB019; opacity: 0.2;"></div>
                            <span>Tasa %</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div id="billing-collection-rate-chart-{{ $order ?? 'default' }}"></div>
        @endif
    </div>

    <style>
        .loading-skeleton {
            padding: 10px 0;
        }

        .skeleton-chart-container {
            padding: 20px 10px;
        }

        .skeleton-bar-chart {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 280px;
            gap: 8px;
            margin-bottom: 10px;
            padding: 0 10px;
        }

        .skeleton-bar-group {
            flex: 1;
            display: flex;
            gap: 2px;
            align-items: flex-end;
            justify-content: center;
        }

        .skeleton-bar {
            width: 45%;
            border-radius: 4px 4px 0 0;
            min-height: 30%;
            animation: barPulse 1.5s ease-in-out infinite;
        }

        .skeleton-bar-primary {
            background: #008FFB;
            opacity: 0.15;
            animation-delay: 0s;
        }

        .skeleton-bar-success {
            background: #00E396;
            opacity: 0.15;
            animation-delay: 0.1s;
        }

        .skeleton-labels {
            display: flex;
            justify-content: space-between;
            padding: 0 10px;
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

        .skeleton-legend-item span {
            font-size: 12px;
            color: #6b7280;
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
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            let chartInstance = null;

            function renderChart(chartData) {
                setTimeout(() => {
                    const divElement = document.querySelector("#billing-collection-rate-chart-{{ $order ?? 'default' }}");

                    if (!divElement) {
                        console.error('❌ Element #billing-collection-rate-chart-{{ $order ?? 'default' }} not found');
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

                    console.log('✅ Rendering Billing vs Collections chart');

                    const options = {
                        series: [
                            {
                                name: 'Facturado',
                                type: 'column',
                                data: chartData.billing || []
                            },
                            {
                                name: 'Cobrado',
                                type: 'column',
                                data: chartData.collections || []
                            },
                            {
                                name: 'Tasa de Cobro (%)',
                                type: 'line',
                                data: chartData.collectionRate || []
                            }
                        ],
                        chart: {
                            height: 350,
                            type: 'line',
                            stacked: false,
                            toolbar: {
                                show: true,
                                tools: {
                                    download: true,
                                    zoom: false,
                                }
                            },
                            animations: {
                                enabled: true,
                                easing: 'easeinout',
                                speed: 800,
                            }
                        },
                        colors: ['#008FFB', '#00E396', '#FEB019'],
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '70%',
                                borderRadius: 4,
                            }
                        },
                        stroke: {
                            width: [0, 0, 3],
                            curve: 'smooth'
                        },
                        dataLabels: {
                            enabled: false
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
                        yaxis: [
                            {
                                title: {
                                    text: 'Monto ($USD)',
                                    style: {
                                        fontSize: '12px',
                                        fontWeight: 600,
                                        color: '#008FFB'
                                    }
                                },
                                labels: {
                                    style: {
                                        colors: '#008FFB'
                                    },
                                    formatter: function(value) {
                                        if (value === null || value === undefined) return '$0';
                                        return '$' + value.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                                    }
                                }
                            },
                            {
                                seriesName: 'Facturado',
                                show: false
                            },
                            {
                                opposite: true,
                                title: {
                                    text: 'Tasa de Cobro (%)',
                                    style: {
                                        fontSize: '12px',
                                        fontWeight: 600,
                                        color: '#FEB019'
                                    }
                                },
                                labels: {
                                    style: {
                                        colors: '#FEB019'
                                    },
                                    formatter: function(value) {
                                        if (value === null || value === undefined) return '0%';
                                        return value.toFixed(1) + '%';
                                    }
                                },
                                min: 0,
                                max: 100
                            }
                        ],
                        tooltip: {
                            shared: true,
                            intersect: false,
                            y: [
                                {
                                    formatter: function(value) {
                                        if (value === null || value === undefined) return '$0.00';
                                        return '$' + value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                                    }
                                },
                                {
                                    formatter: function(value) {
                                        if (value === null || value === undefined) return '$0.00';
                                        return '$' + value.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                                    }
                                },
                                {
                                    formatter: function(value) {
                                        if (value === null || value === undefined) return '0%';
                                        return value.toFixed(1) + '%';
                                    }
                                }
                            ]
                        },
                        legend: {
                            position: 'top',
                            horizontalAlign: 'center',
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
                        markers: {
                            size: 0,
                            hover: {
                                size: 5
                            }
                        }
                    };

                    try {
                        chartInstance = new ApexCharts(divElement, options);
                        chartInstance.render().then(() => {
                            console.log('✅ Billing vs Collections Chart rendered successfully');
                        }).catch(error => {
                            console.error('❌ Error rendering chart:', error);
                        });
                    } catch (error) {
                        console.error('❌ Error creating ApexCharts:', error);
                    }
                }, 200);
            }

            Livewire.on('loadBillingCollectionRateChart', (event) => {
                const chartData = Array.isArray(event) ? event[0] : event;
                renderChart(chartData);
            });

            Livewire.on('updateBillingCollectionRateChart', (event) => {
                const chartData = Array.isArray(event) ? event[0] : event;
                renderChart(chartData);
            });
        });
    </script>
</div>
