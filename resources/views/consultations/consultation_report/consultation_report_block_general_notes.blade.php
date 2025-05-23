<!---------------- GENERAL NOTES ------------------------------------->
@if(!empty($data['general-notes']))
    @component($table_component,['title'=>trans('consultation.consultation_general_notes').":"])
        <div class="paragraph">{{ line_if_empty($data['general-notes'])  }}</div>
    @endcomponent
@endif