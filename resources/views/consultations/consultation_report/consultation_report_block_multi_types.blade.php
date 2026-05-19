<!---------------- MULTI TYPES - IMAGENES - PROCEDIMIENTOS - LABORATORIOS Y DMES -------------------------------->
@if($data->serviceRequests()->count()>0)
@foreach($data['multitypes_selecto'] as $type)
    @component($table_component,['title'=>trans('consultation.'.strtolower($type)."_exams")])
        <table class="result-table" cellspacing="no" cellpadding="0">
            <tr class="table-inner-content-head">
                <td class="upper">{{__('consultation.cpt_code')}}</td>
                <td class="upper">{{ __('consultation.description') }}</td>
            </tr>
            @foreach($data->serviceRequests()->whereServiceType($type)->get() as $sr)
                <tr class="table-contents">
                    <td>{{$sr->code}}</td>
                    <td>
                        {{$sr->cpt->description_es}}
                        @if($sr->performed_in_consultation || $sr->procedure_notes)
                            <br/>Nota :{{$sr->procedure_notes}}
                       @endif
                    </td>
                </tr>
            @endforeach
        </table>
    @endcomponent
@endforeach
@endif
