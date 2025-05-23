<!---------------- DISABILITIES ------------------------------------->

@component($table_component,['title'=>trans('consultation.disabilities')])
    <table class="result-table" cellspacing="no" cellpadding="0">
        <tr class="table-inner-content-head">
            <td class="upper">{{ __('consultation.cpt_code') }}</td>
            <td class="upper">{{ __('consultation.description') }}</td>
        </tr>
        {!! list_array_as_table($consultation_disabilities) !!}
    </table>
    {{--}}
    @if(strlen($data['disabilities-notes'])>1)
        <hr>
        <div class="notes-title-label">{{__('consultation.disabilities_notes')}}:</div>
        <div class="paragraph">{{ line_if_empty($data['disabilities-notes'])  }}</div>
    @endif
    {{--}}
@endcomponent
