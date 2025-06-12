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
   
if ($('#activity-chart-appointment').length > 0) {
var sColStacked = {
    chart: {
        height: 230,
        type: 'bar',
        stacked: false,
        toolbar: {
          show: false,
        }
    },
    // colors: ['#4361ee', '#888ea8', '#e3e4eb', '#d3d3d3'],
    responsive: [{
        breakpoint: 480,
        options: {
            legend: {
                position: 'bottom',
                offsetX: -10,
                offsetY: 0
            }
        }
    }],
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
        data: [20, 30, 41, 67, 22, 43, 40,10,30,20,40]
    },{
        name: 'High',
		color: '#2E37A4',
        data: [13, 23, 20, 8, 13, 27, 30,25,10,15,20]
    }],
    xaxis: {
        categories: ['Jan','Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    },
	
}

var chart = new ApexCharts(
    document.querySelector("#activity-chart-appointment"),
    sColStacked
);

chart.render();
}
</script>
@endpush
