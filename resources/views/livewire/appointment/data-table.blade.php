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
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                            <tr>
                                <th data-column="id" data-priority="1">
                                    <x-table-sort-button title="ID" columnName="appointments.id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="patient" data-priority="2">
                                    <x-table-sort-button title="{{__('appointment.patient')}}" columnName=""/>
                                </th>
                                <th data-column="practitioner" data-priority="3">
                                    <x-table-sort-button title="{{__('appointment.doctor')}}" columnName=""/>
                                </th>
                                <th data-column="status" data-priority="4">
                                    <x-table-sort-button title="{{__('appointment.status')}}" columnName="appointments.status" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="service_type" data-priority="5">
                                    <x-table-sort-button title="{{__('appointment.type')}}" columnName="appointments.service_type" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="branch" data-priority="6">
                                    <x-table-sort-button title="{{__('appointment.branch')}}" columnName="" />
                                </th>
                                <th data-column="consulting_room" data-priority="7">
                                    <x-table-sort-button title="{{__('appointment.consultorio')}}" columnName=""/>
                                </th>
                                <th data-column="date" data-priority="8">
                                    <x-table-sort-button title="{{__('appointment.date')}}" columnName="appointments.start" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="time" data-priority="9">
                                    <x-table-sort-button title="{{__('appointment.time')}}" columnName=""/>
                                </th>
                                <th data-column="acciones" data-priority="1" class="text-end">
                                    <x-table-sort-button title="{{ __('appointment.actions') }}" columnName=""/>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $appointment)
                                <tr class="table-row" data-row-id="{{ $appointment->id }}">
                                    <td data-column="id" data-priority="1" data-label="ID">
                                        <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                            <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                        </span>
                                        <span class="cell-content">{{$appointment->id}}</span>
                                    </td>
                                    <td data-column="patient" data-priority="2" data-label="{{__('appointment.patient')}}">
                                        <span class="cell-content">{!!  $appointment->patient->profile_name !!}</span>
                                    </td>
                                    <td data-column="practitioner" data-priority="3" data-label="{{__('appointment.doctor')}}">
                                        <span class="cell-content">{!!  $appointment->practitioner->profile_name !!}</span>
                                    </td>
                                    <td data-column="status" data-priority="4" data-label="{{__('appointment.status')}}">
                                        <span class="cell-content"><livewire:appointment.status appointment_id="{{$appointment->id}}" wire:key="{{$appointment->id}}"/></span>
                                    </td>
                                    <td data-column="service_type" data-priority="5" data-label="{{__('appointment.type')}}">
                                        <span class="cell-content">{{ $appointment->service_type }}</span>
                                    </td>
                                    <td data-column="branch" data-priority="6" data-label="{{__('appointment.branch')}}">
                                        <span class="cell-content">{{ $appointment->consultingRoom->branch->name }}</span>
                                    </td>
                                    <td data-column="consulting_room" data-priority="7" data-label="{{__('appointment.consultorio')}}">
                                        <span class="cell-content">{{ $appointment->consultingRoom->name }}</span>
                                    </td>
                                    <td data-column="date" data-priority="8" data-label="{{__('appointment.date')}}">
                                        <span class="cell-content">{{ \Carbon\Carbon::parse($appointment->start)->format('d-m-Y') }}</span>
                                    </td>
                                    <td data-column="time" data-priority="9" data-label="{{__('appointment.time')}}">
                                        <span class="cell-content">{{ \Carbon\Carbon::parse($appointment->start)->format('H:i') }} - {{ \Carbon\Carbon::parse($appointment->end)->format('H:i') }}</span>
                                    </td>
                                    <td data-column="acciones" data-priority="1" data-label="{{ __('appointment.actions') }}" class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button
                                                wire:click="$dispatch('showStatusHistory', { appointmentId: {{ $appointment->id }} })"
                                                class="btn btn-info btn-sm"
                                                title="{{ __('appointment.view_status_history') }}"
                                                type="button">
                                                <i class="fa-solid fa-clock-rotate-left"></i>
                                            </button>
                                            @if(auth()->user()->can('edit',$appointment))
                                                <a  wire:click="editAppointment({{$appointment->id}})" style="cursor: pointer;" class="btn btn-success btn-sm" title="{{__('generic.edit')}}">
                                                    <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                </a>
                                            @endif
                                        </div>
                                        <script>
                                            document.addEventListener('livewire:initialized', () => {
                                                Livewire.on('showToastr{{$appointment->id}}', (event) => {
                                                    toastr[event[0].type](event[0].message, '', {
                                                        closeButton: true,
                                                        progressBar: true,
                                                        positionClass: 'toast-top-right',
                                                        timeOut: 5000,
                                                        onHidden: function() {
                                                            @if($appointment->status->value =='checked-in')
                                                            window.location.href = '{{route('consultation.show',$appointment->id)}}'; // Replace with your desired URL
                                                            @endif
                                                        }
                                                    });
                                                });
                                            });
                                        </script>
                                    </td>
                                </tr>
                                <!-- Hidden row for expanded details -->
                                <tr class="row-details d-none" data-parent-row="{{ $appointment->id }}">
                                    <td colspan="10" class="p-3 bg-light">
                                        <div class="row-details-content">
                                            <!-- Details will be populated by JavaScript -->
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination',['data'=>$data])

                </div>
            </div>
        </div>
    </div>
    <livewire:appointment.modal-save wire:model="showModal" :title="$modalTitle"/>
    <livewire:appointment.status-history-modal />

    {{-- Modal de Asignación Manual de Slot --}}
    <livewire:appointment.manual-slot-assignment />

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
