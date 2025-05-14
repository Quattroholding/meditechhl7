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
                            {{ route('appointment.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('id')">Id  @if ($sortDirection === 'asc') ▲ @else ▼ @endif</th>
                                <th>{{__('appointment.patient')}}</th>
                                <th>{{__('appointment.doctor')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('status')">{{__('appointment.status')}}  @if ($sortDirection === 'asc') ▲ @else ▼ @endif</th>
                                <th>{{__('appointment.type')}}</th>
                                <th>{{__('appointment.consultorio')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('start_date')">{{__('appointment.date')}}  @if ($sortDirection === 'asc') ▲ @else ▼ @endif</th>
                                <th>{{__('appointment.time')}}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $appointment)
                                <tr>
                                    <td>{{$appointment->id}}</td>
                                    <td>{!!  $appointment->patient->profile_name !!}</td>
                                    <td>{!!  $appointment->practitioner->user->profile_name !!} </td>
                                    <td>
                                        @if(in_array($appointment->status,['booked','arrived','fulfilled','proposed','pending']))
                                        <div class="btn-group" role="group">
                                            <button id="btngroupverticaldrop1" type="button"
                                                    class="btn  dropdown-toggle
                                                    @if($appointment->status=='fulfilled') btn-success
                                                    @elseif($appointment->status=='booked') btn-warning
                                                    @elseif(in_array($appointment->status,['proposed','pending'])) btn-dark
                                                    @else btn-primary @endif"
                                                    data-bs-toggle="dropdown" aria-expanded="false">   {{ __('appointment.status.'.$appointment->status) }}  </button>
                                            <div class="dropdown-menu" aria-labelledby="btngroupverticaldrop1" style="">
                                                @if($appointment->status=='booked')
                                                    <a class="dropdown-item" href="#" ><i class="fa fa-door-open"></i> {{__('Llegada')}}</a>
                                                    <a class="dropdown-item" href="#" ><i class="fa fa-door-closed"></i> {{__('No Asistio')}}</a>
                                                    <a class="dropdown-item" href="#" ><i class="fa fa-close"></i> {{__('Cancelar')}}</a>
                                                    <a class="dropdown-item" href="#" ><i class="fa fa-warning"></i> {{__('Ingresado por error')}}</a>
                                                @elseif($appointment->status=='arrived')
                                                    <a class="dropdown-item" href="#" ><i class="fa fa-clock-o"></i> {{__('Inciar Consulta')}}</a>
                                                    <a class="dropdown-item" href="#" ><i class="fa fa-close"></i> {{__('Cancelar')}}</a>
                                                    <a class="dropdown-item" href="#" ><i class="fa fa-warning"></i> {{__('Ingresado por error')}}</a>
                                                @elseif($appointment->status=='fulfilled')
                                                    <a class="dropdown-item" href="{{route('consultation.show',$appointment->id)}}" ><i class="fa fa-eye"></i> {{__('Ver Consulta')}}</a>
                                                @elseif(in_array($appointment->status,['proposed','pending']))
                                                    <a class="dropdown-item" href="#" ><i class="fa fa-door-open"></i> {{__('Confirmar')}}</a>
                                                    <a class="dropdown-item" href="#" ><i class="fa fa-close"></i> {{__('Cancelar')}}</a>
                                                    <a class="dropdown-item" href="#" ><i class="fa fa-warning"></i> {{__('Ingresado por error')}}</a>
                                                @endif
                                            </div>
                                        </div>
                                        @else
                                            <button type="button" class="btn btn-danger">   {{ __('appointment.status.'.$appointment->status) }}  </button>
                                        @endif
                                    </td>
                                    <td>{{ $appointment->service_type }}</td>
                                    <td>{{ $appointment->consultingRoom->name }}</a></td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->start)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->start)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end)->format('H:i') }}</td>
                                    <td class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="javascript:;" class="action-icon dropdown-toggle"  data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item"  href="{{ route('appointment.edit',$appointment->id) }}">  <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                    {{__('generic.edit')}}
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal" data-bs-target="#delete_appointment"><i class="fa fa-trash-alt m-r-5"></i> {{__('generic.delete')}}</a>
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
