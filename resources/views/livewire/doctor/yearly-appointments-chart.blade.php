<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">
            <i class="fas fa-calendar"></i> {{ __('Citas del Año ') . date('Y') }}
        </h4>
        <p class="text-muted small" style="color: #fff !important;">Citas registradas de Enero a {{ now()->format('F') }}</p>
    </div>

    <div class="card-body">
        @if($isLoading)
            <div class="loading-skeleton">
                <div class="skeleton-chart-container">
                    <div class="skeleton-bar-chart">
                        <div class="skeleton-bar" style="height: 45%;"></div>
                        <div class="skeleton-bar" style="height: 60%;"></div>
                        <div class="skeleton-bar" style="height: 35%;"></div>
                        <div class="skeleton-bar" style="height: 75%;"></div>
                        <div class="skeleton-bar" style="height: 50%;"></div>
                        <div class="skeleton-bar" style="height: 85%;"></div>
                        <div class="skeleton-bar" style="height: 40%;"></div>
                        <div class="skeleton-bar" style="height: 65%;"></div>
                        <div class="skeleton-bar" style="height: 55%;"></div>
                        <div class="skeleton-bar" style="height: 70%;"></div>
                        <div class="skeleton-bar" style="height: 48%;"></div>
                        <div class="skeleton-bar" style="height: 62%;"></div>
                    </div>
                    <div class="skeleton-x-axis"></div>
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

        .skeleton-bar-chart {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            height: 250px;
            gap: 6px;
            margin-bottom: 5px;
        }

        .skeleton-bar {
            flex: 1;
            background: linear-gradient(180deg, #2E37A4 0%, #5865F2 100%);
            opacity: 0.15;
            border-radius: 4px 4px 0 0;
            min-height: 30%;
            animation: barPulse 1.5s ease-in-out infinite;
        }

        .skeleton-bar:nth-child(1) { animation-delay: 0s; }
        .skeleton-bar:nth-child(2) { animation-delay: 0.1s; }
        .skeleton-bar:nth-child(3) { animation-delay: 0.2s; }
        .skeleton-bar:nth-child(4) { animation-delay: 0.3s; }
        .skeleton-bar:nth-child(5) { animation-delay: 0.4s; }
        .skeleton-bar:nth-child(6) { animation-delay: 0.5s; }
        .skeleton-bar:nth-child(7) { animation-delay: 0.6s; }
        .skeleton-bar:nth-child(8) { animation-delay: 0.7s; }
        .skeleton-bar:nth-child(9) { animation-delay: 0.8s; }
        .skeleton-bar:nth-child(10) { animation-delay: 0.9s; }
        .skeleton-bar:nth-child(11) { animation-delay: 1s; }
        .skeleton-bar:nth-child(12) { animation-delay: 1.1s; }

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
            50% { opacity: 0.3; }
        }

        @keyframes labelPulse {
            0%, 100% { opacity: 0.6; }
            50% { opacity: 0.8; }
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            let chartInstance = null;

            Livewire.on('loadYearlyAppointmentsChart', (data) => {
                const categories = data[0].categories;
                const chartData = data[0].data;

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

                    const options = {
                        chart: {
                            height: 300,
                            type: 'bar',
                            toolbar: {
                                show: false,
                            }
                        },
                        colors: ['#2E37A4'],
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '55%',
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
                        series: [{
                            name: 'Citas',
                            data: chartData
                        }],
                        xaxis: {
                            categories: categories,
                        },
                        yaxis: {
                            title: {
                                text: 'Número de Citas'
                            }
                        },
                        fill: {
                            opacity: 1
                        },
                        tooltip: {
                            y: {
                                formatter: function (val) {
                                    return val + " citas"
                                }
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
