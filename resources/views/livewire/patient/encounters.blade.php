<div>
    <table class="table border-0 custom-table comman-table mb-0">
        <thead>
        <tr>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('encounter.identifier')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('encounter.practitioner')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('encounter.start')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('encounter.end')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('encounter.status')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('encounter.reason')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $row)
            <tr class="">
                <td><a href="{{route('consultation.show',$row->appointment_id)}}" target="_blank"> {{$row->identifier}}</a></td>
                <td>{{$row->practitioner->name}}</td>
                <td>{{$row->start}}</td>
                <td>{{$row->end}}</td>
                <td>{{$row->status}}</td>
                <td>{{$row->reason}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3" class="float-right">
        {{ $data->links() }}
    </div>
    <p>&nbsp;</p>
</div>
