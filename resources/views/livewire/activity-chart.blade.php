<div class="card">
    <div class="card-body">
        <div class="chart-title patient-visit">
            <h4>Activity Chart</h4>
            <div>
                <ul class="nav chat-user-total">
                    <li><i class="fa fa-circle low-users" aria-hidden="true"></i>Low</li>
                    <li><i class="fa fa-circle current-users" aria-hidden="true"></i>High</li>
                </ul>
            </div>
            <div class="form-group mb-0">
                <select wire:model="selectedOption1" class="form-control select" id="timeRange">
                    @foreach (['This Week', 'Last Week', 'This Month', 'Last Month'] as $option)
                        <option value="{{ $option }}">{{ $option }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div id="activity-chart-appointment"></div>
    </div>
</div>

@push('scripts')

<script>
    document.addEventListener('livewire:load', function () {
        console.log('entró');
        var chart;
        
        function updateChart(lowData, highData, categories) {
            if (chart) {
                chart.destroy();
            }

            var options = {
                chart: {
                    height: 230,
                    type: 'bar',
                    stacked: false,
                    toolbar: {
                        show: false,
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 6,
                    colors: ['transparent']
                },
                series: [{
                    name: 'Low',
                    color: '#D5D7ED',
                    data: lowData
                }, {
                    name: 'High',
                    color: '#2E37A4',
                    data: highData
                }],
                xaxis: {
                    categories: categories,
                },
            };

            chart = new ApexCharts(
                document.querySelector("#activity-chart-appointment"),
                options
            );

            chart.render();
        }
 Livewire.on('updateChart', (data) => {
            console.log('Event received:', data); // Verifica que el evento se reciba correctamente
            updateChart(data.lowData, data.highData, data.categories);
        });
        // Initial chart load
        console.log(@json($lowData), @json($highData), @json($categories));
        updateChart(@json($lowData), @json($highData), @json($categories));
    });
</script>
@endpush
