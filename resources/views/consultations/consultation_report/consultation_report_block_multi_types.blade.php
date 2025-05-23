<!---------------- MULTI TYPES - IMAGENES - PROCEDIMIENTOS - LABORATORIOS Y DMES -------------------------------->
@if($data->serviceRequests()->count()>0)
@foreach($data['multitypes_selecto'] as $type)
    @component($table_component,['title'=>trans('consultation.'.strtolower($type)."_exams")])
        <table class="result-table" cellspacing="no" cellpadding="0">
            <tr class="table-inner-content-head">
                <td class="upper">{{__('consultation.cpt_code')}}</td>
                <td class="upper">{{ __('consultation.description') }}</td>
            </tr>
            @foreach($data->serviceRequests()->get() as $sr)
                <tr class="table-contents">
                    <td>{{$sr->code}}</td>
                    <td>{{$sr->cpt->description_es}}</td>
                </tr>
            @endforeach
        </table>
        {{--}}
        @if(strlen($data['necesidad-medica-'.$type])>1)
            <hr>
            <div class="notes-title-label">{{ __('consultation.'.strtolower($type).'_medical_necessity')}}:</div>
            <div class="paragraph">{{ line_if_empty($data['necesidad-medica-'.$type]) }}</div>
        @endif

        @if(strlen($data[$type.'-notes'])>1)
            <hr>
            <div class="notes-title-label">{{ __('consultation.'.strtolower($type).'_notes')}}:</div>
            <div class="paragraph">{{  line_if_empty($data[$type.'-notes'])  }}</div>
        @endif
        {{--}}
    @endcomponent
@endforeach
@endif
