<div>
    <table class="table border-0 custom-table comman-table mb-0">
        <thead>
        <tr>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('physical_exams.identifier_encounter')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('physical_exams.system')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('physical_exams.code')}}</th>

            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('physical_exams.finding')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('physical_exams.effective_date')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $row)
            <tr class="">
                <td><a href="{{route('consultation.show',$row->encounter->appointment_id).'?section=4'}}" target="_blank"> {{$row->encounter->identifier}}</a></td>
                <td>{{$row->observationType->name}}</td>
                <td>{{$row->code}}</td>
                <td>
                    @foreach($row->finding as $key=>$value) {{$value}} @endforeach
                </td>
                <td>{{$row->effective_date}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3" class="float-right">
        {{ $data->links() }}
    </div>
    <p>&nbsp;</p>
</div>
