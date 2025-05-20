<div>
    <table class="table border-0 custom-table comman-table mb-0">
        <thead>
        <tr>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('condition.identifier')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('condition.code')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('condition.description')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('condition.status')}}</th>
            <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('condition.recorded_date')}}</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($data as $row)
            <tr class="">
                <td>{{$row->identifier}}</td>
                <td>{{$row->code}}</td>
                <td>{{$row->icd10Code->description_es}}</td>
                <td>{{$row->clinica_status}}</td>
                <td>{{$row->recorded_date}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <div class="mt-3" class="float-right">
        {{ $data->links() }}
    </div>
    <p>&nbsp;</p>
</div>
