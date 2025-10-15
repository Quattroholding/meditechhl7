<!-- jQuery -->
<script src="{{ URL::asset('/assets/js/jquery-3.7.1.min.js') }}"></script>

<!-- Bootstrap Core JS -->
<script src="{{ URL::asset('/assets/js/bootstrap.bundle.min.js') }}"></script>

<!-- Feather Js -->
<script src="{{ URL::asset('/assets/js/feather.min.js') }}"></script>

<!-- Slimscroll -->
<script src="{{ URL::asset('/assets/js/jquery.slimscroll.js') }}"></script>

<!-- Mask JS -->
<script src="{{URL::asset('/assets/plugins/toastr/toastr.min.js') }}"></script>
<script src="{{URL::asset('/assets/plugins/toastr/toastr.js') }}"></script>

<!-- Select2 Js -->
<script src="{{ URL::asset('/assets/js/select2.min.js') }}"></script>

<!-- Datatables JS -->
<script src="{{ URL::asset('/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ URL::asset('/assets/plugins/datatables/datatables.min.js') }}"></script>

<!-- counterup JS -->
<script src="{{ URL::asset('/assets/js/jquery.waypoints.js') }}"></script>
<script src="{{ URL::asset('/assets/js/jquery.counterup.min.js') }}"></script>

<!-- Apexchart JS -->
<script src="{{ URL::asset('/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
<script src="{{ URL::asset('/assets/plugins/apexchart/chart-data.js') }}"></script>



<!-- Calendar Js -->
<script src="{{ URL::asset('/assets/plugins/simple-calendar/jquery.simple-calendar.js') }}"></script>
<script src="{{ URL::asset('/assets/js/calander.js') }}"></script>

<!-- Circle Progress JS -->
<script src="{{ URL::asset('/assets/js/circle-progress.min.js') }}"></script>

<!-- Slick JS -->
<script src="{{ URL::asset('/assets/plugins/slick/slick.js') }}"></script>

<!-- Datepicker Core JS -->
<script src="{{ URL::asset('/assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/bootstrap-datetimepicker.min.js') }}"></script>

<script src="{{ URL::asset('/assets/plugins/light-gallery/js/lightgallery-all.min.js') }}"></script>

<!-- Summernote JS -->
<script src="{{ URL::asset('/assets/plugins/summernote/summernote-bs5.min.js') }}"></script>

<!-- Ck Editor JS -->
<script src="{{ URL::asset('/assets/js/ckeditor.js') }}"></script>

<!-- Full Calendar -->
<script src="{{ URL::asset('/assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/fullcalendar.min.js') }}"></script>
<script src="{{ URL::asset('/assets/js/jquery.fullcalendar.js') }}"></script>

@if (Route::is(['add-blog', 'edit-blog']))
    <!-- Tagsinput JS -->
    <script src="{{ URL::asset('/assets/js/tagsinput.js') }}"></script>
@endif

@if (Route::is(['seo-settings']))
    <!-- Bootstrap Tagsinput JS -->
    <script src="{{ URL::asset('/assets/plugins/bootstrap-tagsinput/js/bootstrap-tagsinput.js') }}"></script>
@endif

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<!-- Custom JS -->
<script src="{{ URL::asset('/assets/js/app.js') }}"></script>

<script src="{{ URL::asset('/assets/js/custom.js?time='.time()) }}"></script>

<!-- Responsive Table JavaScript -->
<script src="{{ asset('assets/js/responsive-table.js') }}"></script>

<!-- Appointment Broadcast Listener -->
@if(auth()->user()->hasRole('doctor'))
    <script>
        window.isDoctorRole = true;
        // Force load Echo if not available
        if (typeof window.Echo === 'undefined') {
            console.warn('Echo not loaded via Vite, loading manually...');
        }
    </script>
    <script src="{{ URL::asset('/assets/js/appointment-broadcast.js?time='.time()) }}"></script>
@endif


<!-- Dashboard Animations JS -->
@if (Request::is('dashboard*') || Route::is(['admin.dashboard', 'doctor.dashboard', 'patient.dashboard']))
<script src="{{ URL::asset('/assets/js/dashboard-animations.js?time='.time()) }}"></script>
@endif

<!-- Vital Signs Charts Alpine Component -->
<script>
    // Define global function for Alpine to use in vital signs charts
    window.createVitalSignsCharts = function(chartData, period) {
        return {
            chartsLoaded: false,
            chartInstances: {},
            chartData: chartData,
            period: period,

            init() {
                console.log('🎯 Alpine component initialized');
                console.log('📊 Chart data:', this.chartData);
                console.log('🔑 Period:', this.period);

                // Esperar a que el DOM y ApexCharts estén listos
                this.$nextTick(() => {
                    setTimeout(() => {
                        this.renderCharts();
                    }, 500);
                });
            },

            renderCharts() {
                console.log('🔄 Attempting to render charts...');

                // Verificar ApexCharts
                if (typeof ApexCharts === 'undefined') {
                    console.error('❌ ApexCharts not loaded, retrying...');
                    setTimeout(() => this.renderCharts(), 500);
                    return;
                }

                // Verificar elementos
                const bpEl = document.getElementById('bloodPressureChart-' + this.period);
                const hrEl = document.getElementById('heartRateChart-' + this.period);
                const rrEl = document.getElementById('respiratoryRateChart-' + this.period);

                console.log('🔍 Elements check:', {
                    bloodPressure: !!bpEl,
                    heartRate: !!hrEl,
                    respiratory: !!rrEl
                });

                if (!bpEl && !hrEl && !rrEl) {
                    console.error('❌ No chart elements found, retrying...');
                    setTimeout(() => this.renderCharts(), 500);
                    return;
                }

                console.log('✅ Elements found, rendering charts...');

                // Destruir instancias previas
                if (this.chartInstances.bloodPressure) this.chartInstances.bloodPressure.destroy();
                if (this.chartInstances.heartRate) this.chartInstances.heartRate.destroy();
                if (this.chartInstances.respiratory) this.chartInstances.respiratory.destroy();

                // Renderizar cada gráfica
                this.renderBloodPressureChart();
                this.renderHeartRateChart();
                this.renderRespiratoryRateChart();

                // Mostrar las gráficas
                this.chartsLoaded = true;
                console.log('🎉 All charts rendered successfully!');
            },

            renderBloodPressureChart() {
                if (!this.chartData.bloodPressure || !this.chartData.bloodPressure.dates || this.chartData.bloodPressure.dates.length === 0) {
                    return;
                }

                console.log('📈 Rendering Blood Pressure Chart...');

                const bpEl = document.getElementById('bloodPressureChart-' + this.period);
                if (!bpEl) return;

                const options = {
                    series: [{
                        name: 'Sistólica',
                        data: this.chartData.bloodPressure.systolic,
                        color: '#dc2626'
                    }, {
                        name: 'Diastólica',
                        data: this.chartData.bloodPressure.diastolic,
                        color: '#f59e0b'
                    }],
                    chart: { height: 350, type: 'line', toolbar: { show: true }, zoom: { enabled: true } },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 5, hover: { size: 7 } },
                    xaxis: {
                        categories: this.chartData.bloodPressure.dates,
                        title: { text: 'Fecha de Consulta' },
                        labels: {
                            formatter: function(value) {
                                const date = new Date(value);
                                return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                            }
                        }
                    },
                    yaxis: { title: { text: 'Presión (mmHg)' }, min: 40, max: 200 },
                    legend: { position: 'top', horizontalAlign: 'left' },
                    tooltip: {
                        x: {
                            formatter: function(value) {
                                const date = new Date(value);
                                return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
                            }
                        },
                        y: { formatter: function(value) { return value + ' mmHg'; } }
                    }
                };

                bpEl.innerHTML = '';
                this.chartInstances.bloodPressure = new ApexCharts(bpEl, options);
                this.chartInstances.bloodPressure.render().then(() => {
                    console.log('✅ Blood Pressure Chart rendered');
                }).catch(err => {
                    console.error('❌ Error:', err);
                });
            },

            renderHeartRateChart() {
                if (!this.chartData.heartRate || !this.chartData.heartRate.dates || this.chartData.heartRate.dates.length === 0) {
                    return;
                }

                console.log('📈 Rendering Heart Rate Chart...');

                const hrEl = document.getElementById('heartRateChart-' + this.period);
                if (!hrEl) return;

                const options = {
                    series: [{
                        name: 'Frecuencia Cardíaca',
                        data: this.chartData.heartRate.values,
                        color: '#dc2626'
                    }],
                    chart: { height: 350, type: 'line', toolbar: { show: true }, zoom: { enabled: true } },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 5, hover: { size: 7 } },
                    xaxis: {
                        categories: this.chartData.heartRate.dates,
                        title: { text: 'Fecha de Consulta' },
                        labels: {
                            formatter: function(value) {
                                const date = new Date(value);
                                return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                            }
                        }
                    },
                    yaxis: { title: { text: 'Frecuencia (bpm)' }, min: 40, max: 160 },
                    tooltip: {
                        x: {
                            formatter: function(value) {
                                const date = new Date(value);
                                return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
                            }
                        },
                        y: { formatter: function(value) { return value + ' bpm'; } }
                    }
                };

                hrEl.innerHTML = '';
                this.chartInstances.heartRate = new ApexCharts(hrEl, options);
                this.chartInstances.heartRate.render().then(() => {
                    console.log('✅ Heart Rate Chart rendered');
                }).catch(err => {
                    console.error('❌ Error:', err);
                });
            },

            renderRespiratoryRateChart() {
                if (!this.chartData.respiratoryRate || !this.chartData.respiratoryRate.dates || this.chartData.respiratoryRate.dates.length === 0) {
                    return;
                }

                console.log('📈 Rendering Respiratory Rate Chart...');

                const rrEl = document.getElementById('respiratoryRateChart-' + this.period);
                if (!rrEl) return;

                const options = {
                    series: [{
                        name: 'Frecuencia Respiratoria',
                        data: this.chartData.respiratoryRate.values,
                        color: '#3b82f6'
                    }],
                    chart: { height: 350, type: 'line', toolbar: { show: true }, zoom: { enabled: true } },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 3 },
                    markers: { size: 5, hover: { size: 7 } },
                    xaxis: {
                        categories: this.chartData.respiratoryRate.dates,
                        title: { text: 'Fecha de Consulta' },
                        labels: {
                            formatter: function(value) {
                                const date = new Date(value);
                                return date.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
                            }
                        }
                    },
                    yaxis: { title: { text: 'Frecuencia (rpm)' }, min: 8, max: 40 },
                    tooltip: {
                        x: {
                            formatter: function(value) {
                                const date = new Date(value);
                                return date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
                            }
                        },
                        y: { formatter: function(value) { return value + ' rpm'; } }
                    }
                };

                rrEl.innerHTML = '';
                this.chartInstances.respiratory = new ApexCharts(rrEl, options);
                this.chartInstances.respiratory.render().then(() => {
                    console.log('✅ Respiratory Rate Chart rendered');
                }).catch(err => {
                    console.error('❌ Error:', err);
                });
            }
        }
    };

    console.log('✅ createVitalSignsCharts function registered globally');
</script>
