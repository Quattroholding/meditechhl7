<div>
    <table class="table border-0 custom-table comman-table mb-0">
        <thead>
        <tr>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('patient.category')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('patient.history_title')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('patient.description')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('patient.status')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('patient.date')}}</th>
        </tr>
        </thead>
        <tbody>

        @foreach ($data as $row)

            <tr class="">
               <td>{{$row->category}}</td>
               <td>{{$row->title}}</td>
               <td>{{$row->description}}</td>
               <td>{{$row->clinical_status}}</td>
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
