<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header',['show_create'=>auth()->user()->can('patients.create')])
                        @slot('title')

                        @endslot
                        @slot('li_1')
                            {{ route('patient.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->
                    <livewire:modal-add-notes wire:model="showModal"/>

                    <!-- Modal de seguros -->
                    @if($showInsuranceModal && $selectedPatientForInsurance)
                        @livewire('patient.add-insurance', ['patient_id' => $selectedPatientForInsurance->id, 'showInsuranceModal' => true, 'hideButton' => true], key('insurance-'.$selectedPatientForInsurance->id))
                    @endif

                    <!-- Modal de agendar cita -->
                    @livewire('appointment.modal-save')
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                            <tr>
                                <th data-column="practitioners.id" data-priority="1">
                                    <x-table-sort-button title="ID" columnName="patients.id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                    <span class="expand-control d-none">  <i class="fas fa-plus-circle text-primary"></i></span>
                                </th>
                                <th data-column="patients.name" data-priority="2"><x-table-sort-button title="{{__('patient.full_name')}}" columnName="patients.name" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th data-column="patients.birth_date" data-priority="3"><x-table-sort-button title="{{__('patient.birthdate')}}" columnName="patients.birth_date" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th data-column="patients.identifier" data-priority="4"><x-table-sort-button title="{{__('patient.full_id_number')}}" columnName="patients.identifier" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th data-column="patients.email" data-priority="5"><x-table-sort-button title="{{__('patient.email')}}" columnName="patients.email" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th data-column="patients.phone" data-priority="6"><x-table-sort-button title="{{__('patient.whatsapp')}}" columnName="patients.phone" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                @canany(['patient.profile','patient.edit','patient.delete','patient.medical_history','patients.add_note','patients.insurance'])
                                <th data-column="acciones" data-priority="7" class="text-end"><x-table-sort-button title="{{__('Acciones')}}" columnName=""/></th>
                                @endcanany
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $patient)
                                <tr class="table-row" data-row-id="{{ $patient->id }}">
                                    <td data-column="id" data-priority="1" data-label="ID">
                                    <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                        <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                    </span>
                                        <span class="cell-content">{{$patient->id}}</span>
                                    </td>
                                    <td data-column="full_name" data-priority="2" data-label="{{__('patient.full_name')}}"><span class="cell-content">{!!  $patient->profile_name !!}</span></td>
                                    <td data-column="birthdate" data-priority="3" data-label="{{__('patient.birthdate')}}"><span class="cell-content">{!!  $patient->birth_date !!} </span></td>
                                    <td data-column="full_id_number" data-priority="4" data-label="{{__('patient.full_id_number')}}"><span class="cell-content">{{ $patient->identifier }}</span></td>
                                    <td data-column="email" data-priority="5" data-label="{{__('patient.email')}}"><span class="cell-content">{{ $patient->email }}</span></td>
                                    <td data-column="whatsapp" data-priority="6" data-label="{{__('patient.whatsapp')}}"><span class="cell-content">{{ $patient->phone }}</span></td>
                                    @canany(['patient.profile','patient.edit','patient.delete','patient.medical_history','patients.add_note','patients.insurance'])
                                    <td data-column="name" data-priority="7" data-label="{{__('Acciones')}}" class="text-end">
                                        <div class="btn-group btn-group-sm">
                                                @can('patients.edit')
                                                    <a  href="{{ route('patient.show',$patient->id) }}" class="btn btn-info btn-sm" title="{{__('generic.show')}}">
                                                        <i  class="fa-solid fa-eye m-r-5  text-white"></i>
                                                    </a>
                                                    <a  href="{{ route('patient.edit',$patient->id) }}" class="btn btn-primary btn-sm" title="{{__('generic.edit')}}">
                                                        <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                    </a>
                                                @endcan
                                                @can('patients.medical_history')
                                                    <a href="{{route('patient.medical_history',$patient->id)}}" class="btn btn-dark btn-sm" title="{{__('patient.medical_history')}}">
                                                        <i  class="fa fa-file-text-o m-r-5"></i>
                                                    </a>
                                                @endcan
                                                @if(auth()->user()->can('profile',$patient))
                                                <a href="{{route('patient.profile',$patient->id)}}"  class="btn btn-success btn-sm" title="{{__('patient.profile')}}">
                                                    <i  class="fa-solid fa-cog m-r-5"></i>
                                                </a>
                                                @endif
                                                @can('patients.insurance')
                                                    <a  wire:click="openInsuranceModal({{ $patient->id }})" class="btn btn-secondary btn-sm" title="{{__('Gestionar Seguros')}}">
                                                        <i  class="fa-solid fa-shield-halved m-r-5  text-white"></i>
                                                    </a>
                                                @endcan
                                                @can('appointments.create')
                                                    <button type="button"
                                                            onclick="Livewire.dispatch('openAppointmentModalWithPatient', { patientId: {{ $patient->id }} })"
                                                            class="btn btn-warning btn-sm"
                                                            title="Agendar Cita">
                                                        <i class="fa-solid fa-calendar-plus m-r-5"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                    </td>
                                    @endcanany
                                </tr>
                                <!-- Hidden row for expanded details -->
                                <tr class="row-details d-none" data-parent-row="{{ $patient->id }}">
                                    <td colspan="8" class="p-3 bg-light">
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
</div>
