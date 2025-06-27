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
                            {{ route('patient.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->
                    @include('partials.message')
                    <livewire:modal-add-notes wire:model="showModal"/>
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th><x-table-sort-button title="ID" columnName="patients.id" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('patient.full_name')}}" columnName="patients.name" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('patient.birthdate')}}" columnName="patients.birth_date" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('patient.full_id_number')}}" columnName="patients.identifier" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('patient.email')}}" columnName="patients.email" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('patient.whatsapp')}}" columnName="patients.phone" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $patient)
                                <tr>
                                    <td>{{$patient->id}}</td>
                                    <td>{!!  $patient->name !!}</td>
                                    <td>{!!  $patient->birth_date !!} </td>
                                    <td>{{ $patient->identifier }}</td>
                                    <td>{{ $patient->email }}</td>
                                    <td>{{ $patient->phone }}</a></td>
                                    <td class="text-end">
                                        <div class="dropdown dropdown-action">
                                            <a href="javascript:;" class="action-icon dropdown-toggle"  data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item"  wire:click="openModalNote({{ $patient->id }})">  <i  class="fa-solid fa-sticky-note m-r-5"></i>
                                                    {{__('patient.add_note')}}
                                                </a>
                                                <a class="dropdown-item"  href="{{route('patient.medical_history',$patient->id)}}">  <i  class="fa-solid fa-eye m-r-5"></i>
                                                    {{__('patient.medical_history')}}
                                                </a>
                                                @if(auth()->user()->can('profile',$patient))
                                                <a class="dropdown-item"  href="{{route('patient.profile',$patient->id)}}">  <i  class="fa-solid fa-eye m-r-5"></i>
                                                    {{__('patient.profile')}}
                                                </a>
                                                @endif
                                                <a class="dropdown-item"  href="{{ route('patient.edit',$patient->id) }}">  <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                    {{__('generic.edit')}}
                                                </a>
                                                <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal" data-bs-target="#delete_patient"><i class="fa fa-trash-alt m-r-5"></i> {{__('generic.delete')}}</a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        {{ $data->links() }}
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>
