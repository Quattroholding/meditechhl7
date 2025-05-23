<!-------------- Medical History -------------------->

<?php

// -------------- Data and Value ----------------------------
$items = [];
foreach ($data->patient->medicalHistories()->whereNotIn('category',['social-history','family-history'])->get() as $mh){
    $items[] = ['label' => trans('consultation.'.$mh->category), 'value' =>$mh->title];
}
/*
$items[] = ['label' => trans('consultation.surgery'), 'value' => $data->patient->medicalHistories()->whereCategory('surgery')->title];
$items[] = ['label' => trans('consultation.allergies'), 'value' => $data['consultation']->allergies];
$items[] = ['label' => trans('consultation.medicine'), 'value' => $data['consultation']->medicine];
$items[] = ['label' => trans('consultation.others'), 'value' => $data['consultation']->others];
*/

$any_value = false;
foreach ($items as $item) {
    if (strlen($item['value']) > 0) {
        $any_value = true;
        break;
    }
}
?>

@if($any_value)
    @component($table_component,['title'=>trans('consultation.medical_history')])
        <table class="result-table result-table-two-columns" cellspacing="no" cellpadding="0">
            @foreach($items as $item)
                @if(strlen($item['value']) > 0 && preg_match('/[a-zA-Z0-9]/', $item['value']))
                    <tr>
                        <td>{{ $item['label'] }}:</td>
                        <td>{{ line_if_empty($item['value']) }}</td>
                    </tr>
                @endif


            @endforeach
        </table>
    @endcomponent
@endif



