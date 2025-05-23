<!---------------- EVALUACION ------------------------------------->
@php $data['consultation']->box='PLAN'; @endphp
@if(current_user()->can('showBox',$data['consultation']))
    @if(!empty($data['evaluacion']))
        @component($table_component,['title'=>"EVALUATION:"])
            <div class="paragraph">{{ line_if_empty($data['evaluacion'])  }}</div>
        @endcomponent
    @endif
@endif