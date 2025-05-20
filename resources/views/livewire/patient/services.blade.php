<div>
    <table class="table border-0 custom-table comman-table mb-0">
        <thead>
        <tr>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.identifier')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.code')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.description')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.service_type')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.status')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.occurrence_start')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $row)
            <tr class="">
                <td><a href="{{route('consultation.show',$row->encounter->appointment_id)}}" target="_blank"> {{$row->encounter->identifier}}</a></td>
                <td>{{$row->code}}</td>
                <td>{{$row->cpt->description_es}}</td>
                <td>{{$row->service_type}} </td>
                <td>{{$row->status}} </td>
                <td>{{$row->occurrence_start}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3" class="float-right">
        {{ $data->links() }}
    </div>
    <p>&nbsp;</p>
</div>
