<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Search bar -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text"
                                       wire:model.live="search"
                                       class="form-control"
                                       placeholder="Buscar por nombre, identificación, email...">
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                            <tr>
                                <th data-column="patients.id" data-priority="1">
                                    <x-table-sort-button title="ID" columnName="patients.id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                    <span class="expand-control d-none">  <i class="fas fa-plus-circle text-primary"></i></span>
                                </th>
                                <th data-column="patients.name" data-priority="2"><x-table-sort-button title="{{__('patient.full_name')}}" columnName="patients.name" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th data-column="patients.birth_date" data-priority="3"><x-table-sort-button title="{{__('patient.birthdate')}}" columnName="patients.birth_date" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th data-column="patients.identifier" data-priority="4"><x-table-sort-button title="{{__('patient.full_id_number')}}" columnName="patients.identifier" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th data-column="patients.email" data-priority="5"><x-table-sort-button title="{{__('patient.email')}}" columnName="patients.email" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th data-column="patients.gender" data-priority="6"><x-table-sort-button title="{{__('patient.gender')}}" columnName="patients.gender" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th data-column="acciones" data-priority="7" class="text-end"><x-table-sort-button title="{{__('Acciones')}}" columnName=""/></th>
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
                                    <td data-column="whatsapp" data-priority="6" data-label="{{__('patient.gender')}}"><span class="cell-content">{{ $patient->gender }}</span></td>
                                    <td data-column="name" data-priority="7" data-label="{{__('Acciones')}}" class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('dermatology.show', $patient->id) }}" class="btn btn-primary btn-sm" title="Ver Dermatología">
                                                <i class="fas fa-stethoscope m-r-5"></i> Dermatología
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Hidden row for expanded details -->
                                <tr class="row-details d-none" data-parent-row="{{ $patient->id }}">
                                    <td colspan="7" class="p-3 bg-light">
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
