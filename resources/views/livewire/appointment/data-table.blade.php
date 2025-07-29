{{--}}<style>
    .action-btn {
            padding: 0px 8px;
            border: none;
            border-radius: 9px;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
    .btn-confirm { background: #3498db; color: white; }
    .btn-start { background: #f39c12; color: white; }
    .btn-complete { background: #27ae60; color: white; }
    .btn-cancel { background: #e8536e; color: white; }
    .btn-edit { background: #9b59b6; color: white; }

</style>{{--}}
<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header',array('show_create'=>false))
                        @slot('title')

                        @endslot
                        @slot('li_1')
                            {{ route('appointment.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                    @include('partials.message')
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th><x-table-sort-button title="ID" columnName="appointments.id" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('appointment.patient')}}" columnName=""/></th>
                                <th><x-table-sort-button title="{{__('appointment.doctor')}}" columnName=""/></th>
                                <th><x-table-sort-button title="{{__('appointment.status')}}" columnName="appointments.status" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('appointment.type')}}" columnName="appointments.service_type" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('appointment.branch')}}" columnName="" /></th>
                                <th><x-table-sort-button title="{{__('appointment.consultorio')}}" columnName=""/></th>
                                <th><x-table-sort-button title="{{__('appointment.date')}}" columnName="appointments.start" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('appointment.time')}}" columnName=""/></th>
                                <th class="text-end"><x-table-sort-button title="{{__('Acciones')}}" columnName=""/></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $appointment)
                                <tr>
                                    <td>{{$appointment->id}}</td>
                                    <td>{!!  $appointment->patient->profile_name !!}</td>
                                    <td>{!!  $appointment->practitioner->profile_name !!} </td>
                                    <td><livewire:appointment.status appointment_id="{{$appointment->id}}" wire:key="{{$appointment->id}}"/> </td>
                                    <td>{{ $appointment->service_type }}</td>
                                    <td>{{ $appointment->consultingRoom->branch->name }}</a></td>
                                    <td>{{ $appointment->consultingRoom->name }}</a></td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->start)->format('d-m-Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($appointment->start)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end)->format('H:i') }}</td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            {{--}}@if(auth()->user()->can('booked',$appointment))
                                                <a wire:click="editAppointment({{$appointment->id}})" class="btn btn-primary btn-sm" title="{{__('appointment.status.confirm')}}">  
                                                    <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                </a>
                                            @endif{{--}}
                                            @if(auth()->user()->can('edit',$appointment))
                                                <a  wire:click="editAppointment({{$appointment->id}})" style="cursor: pointer;" class="btn btn-success btn-sm" title="{{__('generic.edit')}}">  
                                                    <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                </a>
                                            @endif
                                            {{--}}@if(auth()->user()->can('delete',$appointment))
                                                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#delete_appointment" class="btn btn-danger btn-sm" title="{{__('generic.delete')}}">
                                                    🚮
                                                </a>
                                            @endif
                                            @if(auth()->user()->can('booked',$appointment))
                                                <a wire:click="changeStatus('booked')" class="action-btn btn-confirm" title="{{__('Confirmar')}}">✅</a>
                                            @endif
                                            @if(auth()->user()->can('arrived',$appointment))
                                                <a wire:click="changeStatus('arrived')" class="action-btn btn-start" title="{{__('Registrar Llegada')}}">🚪</a>
                                            @endif
                                            @if(auth()->user()->can('noshow',$appointment))
                                                <a wire:click="changeStatus('noshow')" class="action-btn btn-cancel btn-sm" title="{{__('No apareció')}}">👻</a>
                                            @endif
                                            @if(auth()->user()->can('cancelled',$appointment))
                                                <a wire:click="changeStatus('cancelled')" class="action-btn btn-cancel" title="{{__('Cancelar')}}">❌</a>
                                            @endif
                                            @if(auth()->user()->can('entered-in-error',$appointment))
                                                <a wire:click="changeStatus('entered-in-error')" class="action-btn btn-cancel" title="{{__('Ingresado por error')}}">‼️</a>
                                            @endif
                                            @if(auth()->user()->can('checked_in',$appointment))
                                                <a wire:click="changeStatus('checked-in')" class="action-btn btn-start btn-sm" title="{{__('Iniciar Consulta')}}">▶️</a>
                                            @endif
                                            @if(auth()->user()->can('fulfilled',$appointment))
                                                <a href="{{route('consultation.show',$appointment->id)}}" class="action-btn btn-start btn-sm" title="{{__('Llenar Consulta')}}">🩺</a>
                                            @endif
                                            @if(auth()->user()->can('viewConsultation',$appointment))
                                                <a href="{{route('consultation.show',$appointment->id)}}" class="action-btn btn-start btn-sm" title="{{__('Ver Consulta')}}">👁️</a>
                                            @endif{{--}}
                                        </div>
                                        {{--}}<div class="dropdown dropdown-action">
                                            <a href="javascript:;" class="action-icon dropdown-toggle"  data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @if(auth()->user()->can('booked',$appointment))
                                                    <a class="dropdown-item"  wire:click="editAppointment({{$appointment->id}})">  <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                        {{__('appointment.status.confirm')}}
                                                    </a>
                                                @endif
                                                @if(auth()->user()->can('edit',$appointment))
                                                    <a class="dropdown-item"  wire:click="editAppointment({{$appointment->id}})" style="cursor: pointer;">  <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                        {{__('generic.edit')}}
                                                    </a>
                                               @endif
                                                @if(auth()->user()->can('delete',$appointment))
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal" data-bs-target="#delete_appointment"><i class="fa fa-trash-alt m-r-5"></i>
                                                        {{__('generic.delete')}}
                                                    </a>
                                               @endif
                                            </div>
                                        </div>{{--}}
                                        <script>
                                            document.addEventListener('livewire:initialized', () => {
                                                Livewire.on('showToastr{{$appointment->id}}', (event) => {
                                                    toastr[event[0].type](event[0].message, '', {
                                                        closeButton: true,
                                                        progressBar: true,
                                                        positionClass: 'toast-top-right',
                                                        timeOut: 5000,
                                                        onHidden: function() {
                                                            @if($appointment->status =='checked-in')
                                                            window.location.href = '{{route('consultation.show',$appointment->id)}}'; // Replace with your desired URL
                                                            @endif
                                                        }
                                                    });
                                                });
                                            });
                                        </script>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-muted mb-0">
                                Mostrando del {{ $data->firstItem() }} al {{ $data->lastItem() }}
                                de {{ $data->total() }} resultados
                            </p>
                        </div>
                        <div>
                            {{ $data->links('vendor.pagination.custom-pagination') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <livewire:appointment.modal-save wire:model="showModal" :title="$modalTitle"/>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (event) => {
                console.log('Toastr event received:', event);
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                    onHidden: function() {
                        if(event.appointment_id) {
                           window.location.href = '{{url('consultation')}}/'+event.appointment_id; // Replace with your desired URL
                        }
                    }
                });
            });
        });
    </script>
</div>
