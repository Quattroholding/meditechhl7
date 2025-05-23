<!---------------- Physical Exams -------------->

<?php
// -------------- Data and Value ----------------------------
$items = [];
/*
$items[] = ['title' => trans('consultation.constitutional'), 'value' => $data['physical-examination']->constitutional];
$items[] = ['title' => trans('consultation.heent'), 'value' => $data['physical-examination']->heent];
$items[] = ['title' => trans('consultation.neck') , 'value' => $data['physical-examination']->neck];
$items[] = ['title' => trans('consultation.pulmonary'), 'value' => $data['physical-examination']->pulmonary];
$items[] = ['title' => trans('consultation.cardiovascular'), 'value' => $data['physical-examination']->cadiovascular];
$items[] = ['title' => trans('consultation.chest_breast'), 'value' => $data['physical-examination']->chest_breast];
$items[] = ['title' => trans('consultation.gastrointestinal'), 'value' => $data['physical-examination']->gastrointestinal];
$items[] = ['title' => trans('consultation.lymphatic'), 'value' => $data['physical-examination']->lymphatic];
$items[] = ['title' => trans('consultation.musculoskeletal'), 'value' => $data['physical-examination']->musculoskeletal];
$items[] = ['title' => trans('consultation.skin'), 'value' => $data['physical-examination']->skin];
$items[] = ['title' => trans('consultation.neurologic'), 'value' => $data['physical-examination']->neurologic];
$items[] = ['title' => trans('consultation.psychiatric'), 'value' => $data['physical-examination']->psychiatric];
$items[] = ['title' => trans('consultation.expanded_examination'), 'value' => $data['physical-examination']->expanded_examination];
*/

foreach ($data->physicalExams()->get() as $ph){
    foreach ($ph->finding as $key=>$value){
        $items[] = ['title' => $ph->observationType->name, 'value' => $value];
    }
}

$any_value = false;
foreach ($items as $item) {
    if (strlen($item['value']) > 1) {
        $any_value = true;
        break;
    }
}

?>

@if($any_value)

    @component($table_component,['title'=>trans('consultation.physical_exam')])
        <table class="result-table result-table-two-columns" cellspacing="no" cellpadding="0">


            @foreach($items as $item)
                @if(strlen($item['value'])>1)
                    <tr>
                        <td>{{ $item['title'] }}:</td>
                        <td>
                            {{ line_if_empty($item['value']) }}</span>
                        </td>
                    </tr>
                @endif
            @endforeach

        </table>
    @endcomponent

@endif
