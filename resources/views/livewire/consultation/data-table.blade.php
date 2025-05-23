<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header')
                        @slot('title')

                        @endslot
                        @slot('li_1')

                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('id')">Id  @if ($sortDirection === 'asc') ▲ @else ▼ @endif</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('practitioners.name')">{{__('encounter.practitioner')}}  @if ($sortDirection === 'asc') ▲ @else ▼ @endif</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('patients.name')">{{__('encounter.patient')}}  @if ($sortDirection === 'asc') ▲ @else ▼ @endif</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('encounters.status')">{{__('encounter.status')}}  @if ($sortDirection === 'asc') ▲ @else ▼ @endif</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('encounters.start')">{{__('encounter.day')}}  @if ($sortDirection === 'asc') ▲ @else ▼ @endif</th>
                                <th>{{__('encounter.time')}}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $dato)
                                <tr>
                                    <td>{{$dato->id}}</td>
                                    <td>{!!  $dato->practitioner->profile_name !!} </td>
                                    <td>{!!  $dato->patient->profile_name !!}</td>
                                    <td>{!!  $dato->status !!}</td>
                                    <td>{{ \Carbon\Carbon::parse($dato->start)->format('d-m-Y')  }}</td>
                                    <td>{{ $dato->time }}</td>
                                    <td class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="javascript:;" class="action-icon dropdown-toggle"  data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item"  href="{{ route('consultation.show',$dato->appointment_id) }}">  <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                    {{__('generic.edit')}}
                                                </a>
                                                <a class="dropdown-item"  href="{{ route('consultation.download_resumen',$dato->appointment_id) }}" target="_blank">  <i  class="fa-solid fa-file-pdf m-r-5"></i>
                                                    {{__('generic.download')}}
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3" class="float-right">
                            {{ $data->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
