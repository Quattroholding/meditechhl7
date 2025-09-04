<div class="row">
    <div class="col-sm-12">
        @include('partials.message')
        <div class="card card-table show-entire">
            <div class="card-body">
                <!-- Table Header -->
                @php $show_create=false; if(auth()->user()->can('practitioners.create'))  $show_create=true; @endphp
                @component('components.table-header',['show_create'=>$show_create])
                    @slot('title')

                    @endslot
                    @slot('li_1')
                        {{ route('practitioner.create') }}
                    @endslot
                @endcomponent
                <!-- /Table Header -->
                <div class="table-responsive">
                    <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                        <thead>
                        <tr>
                            <th data-column="practitioners.id" data-priority="1">
                                <x-table-sort-button title="ID" columnName="practitioners.id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                <span class="expand-control d-none">  <i class="fas fa-plus-circle text-primary"></i></span>
                            </th>
                            <th data-column="practitioners.name" data-priority="2"><x-table-sort-button title="{{__('doctor.full_name')}}" columnName="practitioners.name" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                            <th data-column="practitioners.birth_date" data-priority="3"><x-table-sort-button title="{{__('doctor.birthdate')}}" columnName="practitioners.birth_date" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                            <th data-column="practitioners.identifier" data-priority="4"><x-table-sort-button title="{{__('doctor.full_id_number')}}" columnName="practitioners.identifier" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                            <th data-column="practitioners.email" data-priority="5"><x-table-sort-button title="{{__('doctor.email')}}" columnName="practitioners.email" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                            <th data-column="practitioners.phone" data-priority="6"><x-table-sort-button title="{{__('doctor.phone')}}" columnName="practitioners.phone" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                            <th data-column="" data-priority="7"><x-table-sort-button title="{{__('doctor.qualifications')}}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                            @canany(['practitioner.profile','practitioner.edit','practitioner.delete'])
                            <th data-column="" data-priority="1" class="text-end"><x-table-sort-button title="{{__('Acciones')}}" columnName=""/></th>
                            @endcanany
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($data as $practitioner)
                            <tr class="table-row" data-row-id="{{ $practitioner->id }}">
                                <td data-column="id" data-priority="1" data-label="ID">
                                    <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                        <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                    </span>
                                    <span class="cell-content">{{$practitioner->id}}</span>
                                </td>
                                <td data-column="name" data-priority="2" data-label="{{__('doctor.full_name')}}">
                                    <span class="cell-content">{!!  $practitioner->profile_name !!}</span>
                                </td>
                                <td data-column="birth_date" data-priority="3" data-label="{{__('doctor.birthdate')}}">
                                    <span class="cell-content">{!!  $practitioner->birth_date !!}</span>
                                </td>
                                <td data-column="identifier" data-priority="4" data-label="{{__('doctor.full_id_number')}}">
                                    <span class="cell-content">{{ $practitioner->identifier }}</span>
                                </td>
                                <td data-column="email" data-priority="5" data-label="{{__('doctor.email')}}">
                                    <span class="cell-content">{{ $practitioner->email }}</span>
                                </td>
                                <td data-column="phone" data-priority="6" data-label="{{__('doctor.phone')}}">
                                    <span class="cell-content">{{ $practitioner->phone }}</span>
                                </td>
                                <td data-column="qualifications" data-priority="7" data-label="{{__('doctor.qualifications')}}">
                                    <span class="cell-content">
                                        @foreach($practitioner->qualifications()->get() as $q)
                                            {{$q->display}}
                                        @endforeach
                                    </span>
                                </td>
                                @canany(['practitioners.profile','practitioners.edit','practitioners.delete'])
                                <td data-column="acciones" data-priority="1" data-label="{{__('Acciones')}}" class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        @can('practitioners.profile')
                                            <a href="{{route('practitioner.profile',$practitioner->id)}}" class="btn btn-info btn-sm" title="{{__('doctor.profile')}}">
                                                <i  class="fa-solid fa-eye m-r-5 text-white"></i>
                                            </a>
                                        @endcan
                                        @can('practitioners.edit')
                                            <a href="{{ route('practitioner.edit',$practitioner->id) }}" class="btn btn-success btn-sm" title="{{__('generic.edit')}}">
                                                <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                            </a>
                                        @endcan
                                        @if(auth()->user()->hasRole('admin') && !$practitioner->user_id)
                                            <button wire:click="createUser({{ $practitioner->id }})"
                                                    class="btn btn-warning btn-sm"
                                                    title="Crear usuario">
                                                <i class="fa-solid fa-user-plus m-r-5"></i>
                                            </button>

                                        @endif
                                            @role('admin')
                                            <livewire:practitioner.manage-insurances practitioner_id="{{$practitioner->id}}" :showBigButton="false" :showSmallButton="true"/>
                                            @endrole
                                    </div>

                                </td>
                                @endcanany

                            </tr>
                            <!-- Hidden row for expanded details -->
                            <tr class="row-details d-none" data-parent-row="{{ $practitioner->id }}">
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
    @if($showModal)
        <!-- Modal -->
        <div class="modal fade show" id="bs-example-modal-lg" tabindex="-1" aria-labelledby="myLargeModalLabel" style="display: block;" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myLargeModalLabel">{{__('doctor.add_qualification')}}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <textarea wire:model.defer="note" rows="5" class="w-full border p-2 rounded mb-4"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button  wire:click="$set('showModal', false)" type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('generic.cancel') }}</button>
                        <button  wire:click="saveNote" type="button" class="btn btn-primary">{{ __('generic.save') }}</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    @endif
</div>
