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
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th><x-table-sort-button title="ID" columnName="id" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('user.full_name')}}" columnName="first_name" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('user.email')}}" columnName="email" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th><x-table-sort-button title="{{__('user.roles')}}" columnName=""/></th>
                                <th><x-table-sort-button title="{{__('user.created_at')}}" columnName="created_at" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
                                <th class="text-end"><x-table-sort-button title="{{__('Acciones')}}" columnName=""/></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($data as $user)
                                <tr>
                                    <td>{{$user->id}}</td>
                                    <td>{!!  $user->profile_name !!}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)

                                            <span class="badge me-1  @if($role->id==2) bg-primary @elseif($role->id==3) bg-success @elseif($role->id==5) bg-warning @endif">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($user->created_at)->format('d-m-Y') }}</td>
                                    <td class="text-end">
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
</div>
