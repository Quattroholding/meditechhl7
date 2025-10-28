<div class="card top-departments">
    <div class="card-header">
        <h4 class="card-title mb-0" style="color: #fff;">{{__('Diagnósticos por Especialidades')}}</h4>
    </div>
    <div class="card-body" style="width: 95%; margin: 0 auto;">
        @foreach($top_specialties as $speciality)
            <div class="activity-top">
                <div class="departments-list">
                    <h4>{!! ucfirst(strtolower($speciality->onset_info))!!} ({{(round($speciality->percentage,2))}}%) </h4>
                    <p>{{ $speciality->specialty }}</p>
                </div>
            </div>
        @endforeach
    </div>
</div>
