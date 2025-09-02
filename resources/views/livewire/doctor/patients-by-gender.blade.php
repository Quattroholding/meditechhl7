<div class="card patient-structure">
    <div class="card-body">
        @if($isLoading)
            <div class="loading-skeleton">
                <div class="skeleton-title mb-3"></div>
                <div class="skeleton-chart"></div>
            </div>
        @else
            <h5 class="text-primary">{{__('Pacientes por género')}}</h5>
            <div id="radial-patients-active"></div>
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

            Livewire.on('loadGraph', (data) => {
                // Accede a las propiedades del objeto
                const malePercentageCount = data[0].maleCount ;
                const femalePercentageCount  = data[0].femaleCount ;
                const unknownPercentageCount  = data[0].unknownCount ;
                const malePercentage = data[0].male;
                const femalePercentage = data[0].female;
                const unknownPercentage = data[0].unknown;

                //console.log('🔄 Loading ApexCharts for patients-by-gender...', data);

                // Esperar a que el DOM esté actualizado y el elemento sea visible
                setTimeout(() => {
                    var divElement = document.querySelector("#radial-patients-active");

                    if (!divElement) {
                        console.error('❌ Element #radial-patients-active not found');
                        return;
                    }

                    // Verificar que ApexCharts esté disponible
                    if (typeof ApexCharts === 'undefined') {
                        console.error('❌ ApexCharts library not loaded');
                        return;
                    }

                    //console.log('✅ Element found and ApexCharts available', divElement);

                    // Destruir el gráfico existente si existe
                    if (chartInstance) {
                        //console.log('🔄 Destroying existing chart');
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
                            parseFloat(malePercentage) || 0 ,
                            parseFloat(femalePercentage) || 0,
                            parseFloat(unknownPercentage) || 0
                        ],
                        labels: [
                            'Male (' + (malePercentageCount || 0) + ')',
                            'Female (' + (femalePercentageCount || 0) + ')',
                            'Unknown (' + (unknownPercentageCount || 0) + ')'
                        ],
                        colors: ['#4F8EF7', '#FF6B9D', '#FFC107'],
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

                    //console.log('📊 Creating chart with data:', donutChart);

                    try {
                        chartInstance = new ApexCharts(divElement, donutChart);
                        chartInstance.render().then(() => {
                            console.log('✅ ApexCharts rendered successfully for patients-by-gender');
                        }).catch(error => {
                            console.error('❌ Error rendering chart:', error);
                        });
                    } catch (error) {
                        console.error('❌ Error creating ApexCharts:', error);
                    }
                }, 200); // Increased delay to ensure DOM is fully updated
            });
        });
    </script>
</div>
