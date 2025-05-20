<div>
    <table class="table border-0 custom-table comman-table mb-0">
        <thead>
        <tr>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.identifier')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.medicine')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.instruction')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.status')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.valid_from')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.valid_to')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $row)
            <tr class="">
                <td><a href="{{route('consultation.show',$row->encounter->appointment_id)}}" target="_blank"> {{$row->encounter->identifier}}</a></td>
                <td>{{$row->medicine->full_name}}</td>
                <td>{{$row->dosage_text}}</td>
                <td>{{$row->status}} </td>
                <td>{{$row->valid_from}} </td>
                <td>{{$row->valid_to}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3" class="float-right">
        {{ $data->links() }}
    </div>
    <p>&nbsp;</p>
</div>
