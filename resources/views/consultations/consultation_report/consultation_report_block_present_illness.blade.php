<!---------------- PRESENT ILLNESS ------------------------------------->

<?php
/*
$label_location = "";
foreach ($data['location-labels'] as $location_label_item) {
    $label_location .= " ".$location_label_item.",";
}

if (substr($label_location, -1) == ',') {
    $label_location = substr($label_location, 0, -1);
}


// ------------ SEVERITY -------------------------------

$label_severity = "";
if (isset($consultation_list[$data['consultation']->severity])) {
    $label_severity = $consultation_list[$data['consultation']->severity];
}


// ------------ Duration -------------------------------

$label_duration = "";
if (isset($consultation_list[$data['consultation']->duration])) {
    $label_duration = $consultation_list[$data['consultation']->duration];
}




// ------------ Timing (Momento) -------------------------------

$label_timing = "";
if (isset($consultation_list[$data['consultation']->timing])) {
    $label_timing = $consultation_list[$data['consultation']->timing];
}


*/

// -------------- Data and Value ----------------------------
$items = [];
//$data['consultation']->text='present_illness';


$items[] = ['label' => trans('consultation.location'), 'value' => $data->presentIllnesses->location];
$items[] = ['label' => trans('consultation.severity'), 'value' => $data->presentIllnesses->severity];
$items[] = ['label' => trans('consultation.duration'), 'value' => $data->presentIllnesses->duration];
$items[] = ['label' => trans('consultation.timing'), 'value' => $data->presentIllnesses->timing];
$items[] = ['label' => trans('consultation.modifying_factors'), 'value' => $data->presentIllnesses->aggravating_factors];
$items[] = ['label' => trans('consultation.symptoms'), 'value' => $data->presentIllnesses->associated_symptoms];
$items[] = ['label' => trans('consultation.description'), 'value' => $data->presentIllnesses->description];


$any_value = false;

foreach ($items as $item) {
    if (strlen($item['value']) > 0) {
        $any_value = true;
        break;
    }
}

?>

@component($table_component,['title'=>trans('consultation.present_illness')])
<?php $exit_text = [];
$exit_text[0] = "";
?>
    <table class="result-table result-table-two-columns" cellspacing="no" cellpadding="0" >
        @foreach($items as $item)
            @if(strlen($item['value'])>0)
                <?php
                $exit_text = explode("\n",$item['value']);
                ?>
                <tr>
                    <td>{!! $item['label'] !!}:</td>
                    <td>{!! line_if_empty(urldecode($exit_text[0])) !!}</td>
                </tr>
            @endif
        @endforeach
    </table>

    @foreach($exit_text as $key=>$text)
        @if($key!=0)
            <table class="result-table-multiple-lines result-table-two-columns" cellspacing="no" cellpadding="0" >
                <tr>
                    <td></td>
                    <td>{!! urldecode($text) !!}</td>
                </tr>
            </table>
        @endif
    @endforeach

@endcomponent




