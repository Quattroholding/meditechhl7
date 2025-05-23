<!---------------- DMES ------------------------------------->
@php $data['consultation']->box='DMES'; @endphp
@if(current_user()->can('showBox',$data['consultation']))
    @if(sizeof($data['consultation-dmes'])>0)
        @component($table_component,['title'=>"DME'S"])
            <table class="result-table" cellspacing="no" cellpadding="0">
                <tr class="table-inner-content-head">
                    <td>DME CODE</td>
                    <td>NAME</td>
                    <td>DIAGNOSTIC</td>
                </tr>
                {!! list_array_as_table($data['consultation-dmes']) !!}
            </table>
            @if(strlen($data['DME'])>1)
                <hr>
                <div class="notes-title-label">DME's notes:</div>
                <div class="paragraph">{{ line_if_empty($data['DME'])  }}</div>
            @endif
        @endcomponent
    @endif
@endif