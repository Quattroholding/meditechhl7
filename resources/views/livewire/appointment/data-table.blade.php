<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header',array('show_create'=>$show_create))
                        @slot('title')

                        @endslot
                        @slot('li_1')
                            {{ route('appointment.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->
                    <div x-data="{ message: '' }"
                    x-on:cita-message.window="message = $event.detail.message; $store.debug.log('Evento recibido: ' + message)">
                    {{--}} <div x-show="message" x-text="message" class="mb-4"></div>{{--}}
                        <div class="alert alert-success alert-dismissible fade show mb-6" x-show="message" x-text="message" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    </div>
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
                                <th></th>
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
                                        <div class="dropdown dropdown-action">
                                            <a href="javascript:;" class="action-icon dropdown-toggle"  data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            @if(auth()->user()->can('booked',$appointment))
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <a class="dropdown-item"  wire:click="editAppointment({{$appointment->id}})">  <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                        {{__('appointment.status.confirm')}}
                                                    </a>
                                                    @endif
                                            @if(auth()->user()->can('edit',$appointment))
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item"  wire:click="editAppointment({{$appointment->id}})" style="cursor: pointer;">  <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                    {{__('generic.edit')}}
                                                </a>
                                            @endif
                                            @if(auth()->user()->can('delete',$appointment))
                                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal" data-bs-target="#delete_appointment"><i class="fa fa-trash-alt m-r-5"></i> {{__('generic.delete')}}</a>
                                            @endif
                                            </div>
                                        </div>
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
</div>
