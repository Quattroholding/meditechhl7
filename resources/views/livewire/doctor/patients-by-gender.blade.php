 <div class="card patient-structure">
    <div class="card-body">
        <h5>{{__('Pacientes por genero')}}</h5>
        <div id="radial-patients-active"></div>
    </div>
</div>
@push('scripts')
<script>

if ($('#radial-patients-active').length > 0) {
var donutChart = {
    chart: {
        height: 290,
        type: 'donut',
        toolbar: {
          show: false,
        }
    },
	 plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '50%'
        },
    },
	dataLabels: {
        enabled: false
    },

    series: [{{$malePatientsPercentage}}, {{$femalePatientsPercentage}}, {{$unknownGenderPercentage}}],
	labels: [
        'Male',
        'Female',
        'Unknown'
    ],
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
}

var donut = new ApexCharts(
    document.querySelector("#radial-patients-active"),
    donutChart
);

donut.render();
}
    </script>
    @endpush
