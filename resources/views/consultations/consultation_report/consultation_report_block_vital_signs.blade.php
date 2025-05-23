<div class="consultation-report-vital-signs">
    <div style="display: block;">
    @component($table_component, ['title' => trans('consultation.vital_signs')])
        <div style="display: flex; flex-wrap: wrap;">
            @foreach($data->vitalSigns()->get() as $sign)
                <div class="values-container">
                    <span class="label upper">{{$sign->observationType->name}}:</span>
                    <span class="value">{{ $sign->value }} {{ $sign->unit }}</span>
                </div>
            @endforeach
        </div>
    @endcomponent
    </div>
</div>
