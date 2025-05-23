<!---------------- DIAGNOSTICS ------------------------------------->
@php $data['consultation']->box='DIAGNOSTICOS'; @endphp
@if(current_user()->can('showBox',$data['consultation']))
    @if(sizeof($data['consultation-diagnostic'])>0)
        @component($table_component,['title'=>trans('consultation.diagnostics')])
            <table class="result-table" cellspacing="no" cellpadding="0">
                <tr class="table-inner-content-head">
                    <td>ICD10</td>
                    <td class="upper">{{__('consultation.primary_diagnosis')}}</td>
                    <td class="upper">{{__('consultation.secondary_diagnosis')}}</td>
                    <td class="upper">{{__('consultation.relationship_primary_secondary')}}</td>
                </tr>
                {!! list_array_as_table($data['consultation-diagnostic']) !!}
            </table>
            @if(strlen($data['disabilities-notes'])>1)
                <hr>
                <div class="notes-title-label">{{__('consultation.diagnostic_notes')}}:</div>
                <div class="paragraph">{{ line_if_empty($data['diagnostic-notes'])  }}</div>
            @endif
        @endcomponent
    @endif
@endif
