<div>
    <table class="table border-0 custom-table comman-table mb-0">
        <thead>
        <tr>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('referrals.identifier')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('referrals.referred_from')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('referrals.referred_to')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('referrals.speciality')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('referrals.reason')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('referrals.occurrence_date')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $row)
            <tr class="">
                <td><a href="{{route('consultation.show',$row->encounter->appointment_id).'?section=9'}}" target="_blank"> {{$row->encounter->identifier}}</a></td>
                <td>{{$row->requester->name}}</td>
                <td>{{$row->referredTo->name}}</td>
                <td>{{$row->speciality->name}} </td>
                <td>{{$row->reason}} </td>
                <td>{{$row->occurrence_date}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3" class="float-right">
        {{ $data->links() }}
    </div>
    <p>&nbsp;</p>
</div>
