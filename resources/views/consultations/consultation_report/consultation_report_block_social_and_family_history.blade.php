<!-- Social history -->

<?php

// -------------- Data and Value ----------------------------
$items = [];
/*
$items[] = ['label' => trans('consultation.family_history'), 'value' => $data['consultation']->family_past];
$items[] = ['label' => trans('consultation.alcohol'), 'value' => $data['consultation']->if_alcohol];
$items[] = ['label' => trans('consultation.drugs'), 'value' => $data['consultation']->if_drugs];
$items[] = ['label' => trans('consultation.tobacco'), 'value' => $data['consultation']->if_tabacco];
$items[] = ['label' => trans('consultation.others'), 'value' => $data['consultation']->if_others2];
*/

foreach ($data->patient->medicalHistories()->whereIn('category',['social-history','family-history'])->get() as $mh){
    $items[] = ['label' => $mh->title, 'value' =>$mh->description];
}

$any_value = false;
foreach ($items as $item) {
    if (strlen($item['value']) > 0) {
        $any_value = true;
        break;
    }
}
?>


@if($any_value)
    @component($table_component,['title'=>trans('consultation.social_family_history')])
        <table class="result-table result-table-two-columns" cellspacing="no" cellpadding="0">
            @foreach($items as $item)
                @if(strlen($item['value'])>0)
                    <tr>
                        <td>{{ $item['label'] }}:</td>
                        <td>{{ line_if_empty($item['value']) }}</td>
                    </tr>
                @endif
            @endforeach

        </table>
    @endcomponent
@endif
