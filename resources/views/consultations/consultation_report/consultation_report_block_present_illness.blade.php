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

// Procesar locations como array JSON
$locations_array = is_string($data->presentIllnesses->locations)
    ? json_decode($data->presentIllnesses->locations, true)
    : $data->presentIllnesses->locations;

$location = '';
if (is_array($locations_array)) {
    $translated_locations = array_map(function($loc) {
        return trans('present_illness.'.$loc);
    }, $locations_array);
    $location = implode(', ', $translated_locations);
}

$severity = !empty($data->presentIllnesses->severity) ? trans('present_illness.'. $data->presentIllnesses->severity) : '';
$duration = !empty($data->presentIllnesses->duration) ? trans('present_illness.'. $data->presentIllnesses->duration) : '';
$timing = !empty($data->presentIllnesses->timing) ? trans('present_illness.'. $data->presentIllnesses->timing) : '';


$items[] = ['label' => trans('consultation.location'), 'value' => $location];
$items[] = ['label' => trans('consultation.severity'), 'value' => $severity];
$items[] = ['label' => trans('consultation.duration'), 'value' =>$duration];
$items[] = ['label' => trans('consultation.timing'), 'value' =>$timing];
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
@if($data->presentIllnesses->locations || $data->presentIllnesses->severity || $data->presentIllnesses->duration || $data->presentIllnesses->timing)
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
                    <td>
                        @if(isset(json_decode($exit_text[0])->location))
                            @foreach(json_decode($exit_text[0]) as $v)
                                {{$v[0]}}
                            @endforeach
                        @else
                          {!! line_if_empty(urldecode($exit_text[0])) !!}
                        @endif
                    </td>
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
@endif



