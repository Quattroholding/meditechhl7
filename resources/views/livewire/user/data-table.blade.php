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
                            {{ route('user.create') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                            <tr>
                                <th data-column="id" data-priority="1">
                                    <x-table-sort-button title="ID" columnName="id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="full_name" data-priority="2">
                                    <x-table-sort-button title="{{__('user.full_name')}}" columnName="first_name" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="email" data-priority="3">
                                    <x-table-sort-button title="{{__('user.email')}}" columnName="email" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="roles" data-priority="4">
                                    <x-table-sort-button title="{{__('user.roles')}}" columnName=""/>
                                </th>
                                <th data-column="created_at" data-priority="5">
                                    <x-table-sort-button title="{{__('user.created_at')}}" columnName="created_at" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="active" data-priority="5">
                                    <x-table-sort-button title="{{__('Estado')}}" columnName="active" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                @canany(['user.edit','users.activate','users.delete'])
                                <th data-column="acciones" data-priority="1" class="text-end">
                                    <x-table-sort-button title="{{__('Acciones')}}" columnName=""/>
                                </th>
                                @endcanany
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $user)
                                <tr class="table-row" data-row-id="{{ $user->id }}">
                                    <td data-column="id" data-priority="1" data-label="ID">
                                        <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                            <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                        </span>
                                        <span class="cell-content">{{$user->id}}</span>
                                    </td>
                                    <td data-column="full_name" data-priority="2" data-label="{{__('user.full_name')}}">
                                        <span class="cell-content">{!!  $user->profile_name !!}</span>
                                    </td>
                                    <td data-column="email" data-priority="3" data-label="{{__('user.email')}}">
                                        <span class="cell-content">{{ $user->email }}</span>
                                    </td>
                                    <td data-column="roles" data-priority="4" data-label="{{__('user.roles')}}">
                                        <span class="cell-content">
                                            @foreach($user->roles as $role)
                                                <span class="badge me-1  @if($role->id==2) bg-primary @elseif($role->id==3) bg-success @elseif($role->id==4) bg-info @elseif($role->id==5) bg-warning @elseif($role->id==6) bg-danger @endif">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </span>
                                    </td>
                                    <td data-column="created_at" data-priority="5" data-label="{{__('user.created_at')}}">
                                        <span class="cell-content">{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') }}</span>
                                    </td>
                                    <td data-column="active" data-priority="5" data-label="{{__('Estado')}}">
                                        <span class="cell-content badge me-1 {{$user->active ? 'bg-success' : 'bg-danger'}}">{{  $user->active ? 'Activo' : 'Inactivo' }}</span>
                                    </td>
                                    @canany(['user.edit','users.activate','users.delete'])
                                    <td data-column="acciones" data-priority="1" data-label="{{__('Acciones')}}" class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @can('users.edit')
                                                <a href="{{ route('user.edit', $user->id) }}" class="btn btn-success btn-sm" title="{{__('generic.edit')}}">
                                                    <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                </a>
                                            @endcan
                                            @can('users.activate')
                                                @if($user->active == false)
                                                    <livewire:user.active-users user_id="{{$user->id}}" wire:key="{{$user->id}}"/>
                                                @endif
                                            @endcan
                                            @can('users.delete' && auth()->user()->id <> $user->id)
                                                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#delete_modal" data-route="{{route('user.destroy', $user->id)}}" class="btn btn-danger btn-sm" title="{{__('generic.delete')}}">
                                                    <i class="fa fa-trash-alt m-r-5"></i>
                                                </a>
                                            @endcan
                                        </div>
                                    </td>
                                    @endcanany
                                </tr>
                                <!-- Hidden row for expanded details -->
                                <tr class="row-details d-none" data-parent-row="{{ $user->id }}">
                                    <td colspan="6" class="p-3 bg-light">
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
