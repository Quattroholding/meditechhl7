<div>
    <!-- Table Header -->
    @component('components.table-header')
        @slot('title')
          {{$title}}
        @endslot
        @slot('li_1')
            {{ route($route_name.'.create') }}
        @endslot
    @endcomponent
    <!-- /Table Header -->
    <div class="table-responsive">
        <table class="table border-0 custom-table comman-table mb-0">
        <thead>
        <tr>
            @foreach ($columns as $column)
                <th class="@if(strtolower($column)=='acciones') text-end @endif"><x-table-sort-button
                     title="{{ __($route_name.'.'.$column) }}" columnName="{{ $column }}" :sortField="$sortField" :sortDirection="$sortDirection"/></th>
            @endforeach
        </thead>
        <tbody>
        @forelse ($data as $row)
            <tr class="">
                @foreach ($columns as $column)
                    <td class="border-b border-gray-100 p-2 @if($column=='acciones') text-end @endif" >
                        @if(App\Models\Helper::urlIsImage($row->$column))
                            <img src="{{$row->$columns}}">
                        @elseif($column=='acciones')
                            <div class="btn-group btn-group-sm">
                                @if(in_array('edit',$actions))
                                    <a href="{{route($route_name.'.edit',$row->id)}}" class="btn btn-success btn-sm" title="{{__('generic.edit')}}">
                                        <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                    </a>
                                @endif
                                @if(in_array('delete',$actions))
                                    <a href="javascript:;" data-bs-toggle="modal"  data-bs-target="#delete_modal" data-route="{{route($route_name.'.destroy',$row->id)}}"class="btn btn-danger btn-sm" title="{{__('generic.delete')}}">
                                        <i class="fa fa-trash-alt m-r-5"></i>
                                    </a>
                                @endif
                            </div>
                            {{--}}<div class="dropdown dropdown-action">
                                <a href="javascript:;" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i   class="fa fa-ellipsis-v"></i></a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if(in_array('edit',$actions))
                                    <a class="dropdown-item" href="{{route($route_name.'.edit',$row->id)}}"><i   class="fa-solid fa-pen-to-square m-r-5"></i> {{__('generic.edit')}}</a>
                                    @endif
                                    @if(in_array('delete',$actions))
                                    <a class="dropdown-item" href="javascript:;" data-bs-toggle="modal"  data-bs-target="#delete_patient"><i class="fa fa-trash-alt m-r-5"></i> {{__('generic.delete')}}</a>
                                    @endif
                                </div>
                            </div>{{--}}
                        @else
                            {!! $row->$column  !!}
                        @endif
                    </td>
                @endforeach
            </tr>
        @empty
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-close fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('Sin resultados de busqueda') }}</h5>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
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
