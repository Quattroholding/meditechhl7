<div class="card patient-structure">
    <div class="card-body">
        @if($isLoading)
            <div class="loading-skeleton">
                <div class="skeleton-title mb-3"></div>
                <div class="skeleton-chart"></div>
            </div>
        @else
            <h5 class="text-base">Pacientes por Rango de Edad</h5>
            <div id="radial-patients-age-block"></div>
        @endif
    </div>

    <style>
        .loading-skeleton {
            animation: pulse 1.5s ease-in-out infinite;
        }

        .skeleton-title {
            height: 20px;
            background: #e9ecef;
            border-radius: 4px;
            width: 80%;
        }

        .skeleton-chart {
            height: 290px;
            background: #e9ecef;
            border-radius: 50%;
            width: 290px;
            margin: 0 auto;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>

    <script>
        document.addEventListener('livewire:initialized', () => {
            let chartInstance = null;

            Livewire.on('loadAgeBlockGraph', (data) => {
                // Accede a las propiedades del objeto
                const age0to12Count = data[0].age0to12Count;
                const age13to17Count = data[0].age13to17Count;
                const age18to25Count = data[0].age18to25Count;
                const age26to59Count = data[0].age26to59Count;
                const age60PlusCount = data[0].age60PlusCount;
                const age0to12Percentage = data[0].age0to12;
                const age13to17Percentage = data[0].age13to17;
                const age18to25Percentage = data[0].age18to25;
                const age26to59Percentage = data[0].age26to59;
                const age60PlusPercentage = data[0].age60Plus;

                // Esperar a que el DOM esté actualizado y el elemento sea visible
                setTimeout(() => {
                    var divElement = document.querySelector("#radial-patients-age-block");

                    if (!divElement) {
                        console.error('❌ Element #radial-patients-age-block not found');
                        return;
                    }

                    // Verificar que ApexCharts esté disponible
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

                    var donutChart = {
                        chart: {
                            height: 290,
                            type: 'donut',
                            toolbar: {
                                show: false,
                            }
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '65%'
                                }
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        series: [
                            parseFloat(age0to12Percentage) || 0,
                            parseFloat(age13to17Percentage) || 0,
                            parseFloat(age18to25Percentage) || 0,
                            parseFloat(age26to59Percentage) || 0,
                            parseFloat(age60PlusPercentage) || 0
                        ],
                        labels: [
                            '0-12 años (' + (age0to12Count || 0) + ')',
                            '13-17 años (' + (age13to17Count || 0) + ')',
                            '18-25 años (' + (age18to25Count || 0) + ')',
                            '26-59 años (' + (age26to59Count || 0) + ')',
                            '60+ años (' + (age60PlusCount || 0) + ')'
                        ],
                        colors: ['#36A2EB', '#4BC0C0', '#FFCE56', '#FF6384', '#36EB42'],
                        responsive: [{
                            breakpoint: 480,
                            options: {
                                chart: {
                                    width: 200
                                },
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }],
                        legend: {
                            position: 'bottom',
                        }
                    };

                    try {
                        chartInstance = new ApexCharts(divElement, donutChart);
                        chartInstance.render().then(() => {
                            console.log('✅ ApexCharts rendered successfully for patients-by-age-block');
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