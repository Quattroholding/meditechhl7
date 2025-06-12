<div class="card patient-structure">
    <div class="card-body">
        <h5>New Patients</h5>
        <h3>{{ $patients }}<span class="{{ $statusClass }}"><img src="{{ URL::asset('/assets/img/icons/' . $icon) }}"
                    alt="" class="me-1">{{ number_format($percentageChange, 1) }}%</span></h3>
    </div>
</div>