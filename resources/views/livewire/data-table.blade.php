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
        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
        <thead>
        <tr>
            @foreach ($columns as $column)
                <th class="@if(strtolower($column)=='acciones') text-end @endif"
                    data-column="{{ $column }}"
                    data-priority="{{ $loop->first || strtolower($column)=='acciones' ? '1' : ($loop->index + 1) }}">
                    @if($loop->first)
                        <span class="expand-control d-none">
                            <i class="fas fa-plus-circle text-primary"></i>
                        </span>
                    @endif
                    <x-table-sort-button
                     title="{{ __($route_name.'.'.$column) }}" columnName="{{ $column }}" :sortField="$sortField" :sortDirection="$sortDirection"/>
                </th>
            @endforeach
        </thead>
        <tbody>
        @forelse ($data as $row)
            <tr class="table-row" data-row-id="{{ $row->id }}">
                @foreach ($columns as $column)
                    <td class="border-b border-gray-100 p-2 @if($column=='acciones') text-end @endif"
                        data-column="{{ $column }}"
                        data-priority="{{ $loop->first || strtolower($column)=='acciones' ? '1' : ($loop->index + 1) }}"
                        data-label="{{ __($route_name.'.'.$column) }}">
                        @if($loop->first)
                            <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                            </span>
                        @endif

                        @if(App\Models\Helper::urlIsImage($row->$column))
                            <img src="{{$row->$column}}" alt="Image" class="img-thumbnail" style="max-width: 50px;">
                        @elseif($column=='acciones')
                            <div class="btn-group btn-group-sm">
                                @if(in_array('edit',$actions))
                                    <a href="{{route($route_name.'.edit',$row->id)}}" class="btn btn-success btn-sm" title="{{__('generic.edit')}}">
                                        <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                    </a>
                                @endif
                                @if(in_array('delete',$actions))
                                    <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#delete_modal" data-route="{{route($route_name.'.destroy',$row->id)}}" class="btn btn-danger btn-sm" title="{{__('generic.delete')}}">
                                        <i class="fa fa-trash-alt m-r-5"></i>
                                    </a>
                                @endif
                            </div>
                        @else
                            <span class="cell-content">{!! $row->$column !!}</span>
                        @endif
                    </td>
                @endforeach
            </tr>
            <!-- Hidden row for expanded details -->
            <tr class="row-details d-none" data-parent-row="{{ $row->id }}">
                <td colspan="{{ count($columns) }}" class="p-3 bg-light">
                    <div class="row-details-content">
                        <!-- Details will be populated by JavaScript -->
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($columns) }}" class="text-center py-4">
                    <div class="d-flex flex-column align-items-center">
                        <i class="fas fa-close fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('Sin resultados de busqueda') }}</h5>
                    </div>
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
    @include('partials.pagination',['data'=>$data])
    </div>

</div>
