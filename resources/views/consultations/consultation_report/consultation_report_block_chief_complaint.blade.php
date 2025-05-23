<!---------------- QUEJA PRINCIPAL -------------------------------->

@if($data->reason != null and $data->reason != '' and $data->reason != '-')
    @component($table_component,['title'=>trans('consultation.chief_complaint')])
        <div class="paragraph">{{ $data->reason }}</div>
    @endcomponent
@endif
