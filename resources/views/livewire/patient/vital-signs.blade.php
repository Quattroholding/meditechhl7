<div>
    <table class="table border-0 custom-table comman-table mb-0">
        <thead>
        <tr>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('vital_signs.identifier_encounter')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('vital_signs.code')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('vital_signs.value')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('vital_signs.unit')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('vital_signs.effective_date')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $row)
            <tr class="">
                <td><a href="{{route('consultation.show',$row->encounter->appointment_id)}}" target="_blank"> {{$row->encounter->identifier}}</a></td>
                <td>{{$row->code}}</td>
                <td>{{$row->value}}</td>
                <td>{{$row->unit}}</td>
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
