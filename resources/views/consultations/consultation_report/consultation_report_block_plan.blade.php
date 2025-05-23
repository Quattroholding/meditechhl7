<!---------------- PLAN ------------------------------------->
@php $data['consultation']->box='PLAN'; @endphp
@if(current_user()->can('showBox',$data['consultation']))
    <!-- if plan is not empty -->
    @if(!empty($data['plan']))
        @component($table_component,['title'=>"PLAN:"])
            <div class="paragraph">{{ line_if_empty($data['plan'])  }}</div>
        @endcomponent
    @endif
@endif