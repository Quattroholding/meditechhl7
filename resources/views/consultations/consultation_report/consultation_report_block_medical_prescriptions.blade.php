<!---------------- MEDICAL PRESCRIPTIONS ------------------------------------->
@if($data->medicationRequests()->count()>0)
@component($table_component,['title'=>trans('consultation.medical_prescriptions')])
    <table class="meds-table" cellspacing="no" border="0" cellpadding="0" style="width:100%">
        <tr class="table-inner-content-head">
            <th style="width:35%">{{__('consultation.name')}}</th>
            <th style="width:65%">Plan</th>
        </tr>
        @foreach($data->medicationRequests()->get() as $mp)
            <tr class="table-contents">
                <td>{{$mp->medicine->full_name}}</td>
                <td>{{$mp->dosage_text}}</td>
            </tr>
        @endforeach
    </table>
@endcomponent
@endif
