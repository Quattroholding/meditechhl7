<!---------------- REFERENCE TO SPECIALIST(s) ------------------------------------->
@if($data->referrals()->count()>0)
@component($table_component,['title'=>trans('consultation.reference_specialist')])
    <table class="result-table" cellspacing="no" cellpadding="0">
        <tr class="table-inner-content-head">
            <td>{{__('consultation.name')}}</td>
            <td>{{__('consultation.observation')}}</td>
        </tr>
        @foreach($data->referrals()->get() as $re)
            <tr class="table-contents">
                <td>{{$re->speciality->name}}</td>
                <td>{{$re->reason}}</td>
            </tr>
        @endforeach
    </table>
    {{--}}
    <div class="notes-title-label">{{__('consultation.medical_necessity')}}:</div>
    <div class="paragraph">{{ line_if_empty($data['necesidad-medica-specialties']) }}</div>
    {{--}}
@endcomponent
@endif
