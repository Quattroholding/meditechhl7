<!---------------- THERAPY ------------------------------------->
@php $data['consultation']->box='TERAPIAS'; @endphp
@if(current_user()->can('showBox',$data['consultation']))
    @if(sizeof($data['consultation-therapy'])>0)
        @component($table_component,['title'=>trans('consultation.therapy')])
            <table class="result-table" cellspacing="no" cellpadding="0">
                <tr class="table-inner-content-head">
                    <td class="upper">{{__('consultation.therapy_needed')}}</td>
                    <td class="upper">{{__('consultation.therapy_amount')}}</td>
                    <td class="upper">{{__('consultation.diagnostic')}}</td>
                </tr>
                {!! list_array_as_table($data['consultation-therapy']) !!}
            </table>
            <hr>
            <div class="notes-title-label">{{__('consultation.medical_necessity')}}:</div>
            <div class="paragraph">{{ line_if_empty($data['therapy-medical-necessity-notes']) }}</div>
            <hr>
            <div class="notes-title-label">{{__('consultation.medical_indication')}}:</div>
            <div class="paragraph">{{ line_if_empty($data['therapy-medical-indication-notes'])  }}</div>
        @endcomponent
    @endif
@endif