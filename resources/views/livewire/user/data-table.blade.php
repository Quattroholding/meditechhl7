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
                                <th data-column="acciones" data-priority="1" class="text-end">
                                    <x-table-sort-button title="{{__('Acciones')}}" columnName=""/>
                                </th>
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
                                                <span class="badge me-1  @if($role->id==2) bg-primary @elseif($role->id==3) bg-success @elseif($role->id==5) bg-warning @endif">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </span>
                                    </td>
                                    <td data-column="created_at" data-priority="5" data-label="{{__('user.created_at')}}">
                                        <span class="cell-content">{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') }}</span>
                                    </td>
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
                                           {{--}} @can('users.activate')
                                                @if($user->active == false)
                                                 <button type="button"
                                                    class="btn btn-warning btn-sm"
                                                    title="{{ __('Reactivar Usuario') }}"
                                                    wire:click.prevent="activateUser({{ $user->id }})"
                                                    wire:loading.attr="disabled"
                                                    wire:target="activateUser({{ $user->id }})">

                                                    <i class="fa fa-check-circle m-r-5"
                                                    wire:loading.remove
                                                    wire:target="activateUser({{ $user->id }})"></i>

                                                    <i class="fa fa-spinner fa-spin m-r-5"
                                                    wire:loading
                                                    wire:target="activateUser({{ $user->id }})"></i>

                                                <span wire:loading.remove wire:target="activateUser({{ $user->id }})">
                                                    {{ __('Activar') }}
                                                </span>
                                                <span wire:loading wire:target="activateUser({{ $user->id }})">
                                                    {{ __('Activando...') }}
                                                </span>
                                            </button>
                                                @endif
                                            @endcan{{--}}
                                            @can('users.delete')
                                                <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#delete_modal" data-route="{{route('user.destroy', $user->id)}}" class="btn btn-danger btn-sm" title="{{__('generic.delete')}}">
                                                    <i class="fa fa-trash-alt m-r-5"></i>
                                                </a>
                                            @endcan
                                        </div>
                                        {{--}}<div class="dropdown dropdown-action">
                                            <a href="javascript:;" class="action-icon dropdown-toggle"  data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                @can('users.edit')
                                                    <a class="dropdown-item" href="{{ route('user.edit', $user->id) }}">
                                                        <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                        {{__('generic.edit')}}
                                                    </a>
                                                @endcan
                                                @can('users.delete')
                                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal" data-bs-target="#delete_user">
                                                        <i class="fa fa-trash-alt m-r-5"></i>
                                                        {{__('generic.delete')}}
                                                    </a>
                                                @endcan
                                            </div>
                                        </div>{{--}}
                                    </td>
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
