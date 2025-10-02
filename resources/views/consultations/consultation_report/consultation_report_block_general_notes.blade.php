<!---------------- GENERAL NOTES ------------------------------------->

@if(!empty($data['general_note']))
    @component($table_component,['title'=>trans('consultation.consultation_general_notes').":"])
        <div class="paragraph">{{ line_if_empty($data['general_note'])  }}</div>
    @endcomponent
@endif
