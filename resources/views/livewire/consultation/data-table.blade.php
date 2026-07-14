<div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header',array('show_create'=>false,'title'=>''))
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>{{__('consultation.data_table.status')}}</label>
                                    <x-select-input wire:model.live="methodFilter" name="metodo" :options="['in-progress'=>__('consultation.data_table.in_progress'),'finished'=>__('consultation.data_table.finished')]" :selected="[]" class="form-select"/>
                                </div>
                            </div>
                        @endslot
                    @endcomponent

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                            <tr>
                                <th data-column="id" data-priority="1" class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('id')">
                                    Id @if ($sortDirection === 'asc') ▲ @else ▼ @endif
                                </th>
                                @if(!auth()->user()->hasRole('doctor'))
                                <th data-column="practitioner" data-priority="2" class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('practitioners.name')">
                                    {{__('encounter.practitioner')}} @if ($sortDirection === 'asc') ▲ @else ▼ @endif
                                </th>
                                @endif
                                <th data-column="patient" data-priority="3" class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('patients.name')">
                                    {{__('encounter.patient')}} @if ($sortDirection === 'asc') ▲ @else ▼ @endif
                                </th>
                                <th data-column="status" data-priority="4" class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('encounters.status')">
                                    {{__('encounter.status')}} @if ($sortDirection === 'asc') ▲ @else ▼ @endif
                                </th>
                                <th data-column="start" data-priority="5" class="border-b border-gray-300 p-2 cursor-pointer" wire:click="sortBy('encounters.start')">
                                    {{__('encounter.day')}} @if ($sortDirection === 'asc') ▲ @else ▼ @endif
                                </th>
                                <th data-column="time" data-priority="6">
                                    {{__('encounter.time')}}
                                </th>
                                <th data-column="acciones" data-priority="1" class="text-end">
                                    <x-table-sort-button title="{{__('consultation.data_table.actions')}}" columnName=""/>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $dato)
                                <tr class="table-row" data-row-id="{{ $dato->id }}">
                                    <td data-column="id" data-priority="1" data-label="ID">
                                        <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                            <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                        </span>
                                        <span class="cell-content">{{$dato->id}}</span>
                                    </td>
                                    @if(!auth()->user()->hasRole('doctor'))
                                    <td data-column="practitioner" data-priority="2" data-label="{{__('encounter.practitioner')}}">
                                        <span class="cell-content">{!!  $dato->practitioner->profile_name !!}</span>
                                    </td>
                                    @endif
                                    <td data-column="patient" data-priority="3" data-label="{{__('encounter.patient')}}">
                                        <span class="cell-content">{!!  $dato->patient->profile_name !!}</span>
                                    </td>
                                    <td data-column="status" data-priority="4" data-label="{{__('encounter.status')}}">
                                        <span class="cell-content">{!!  $dato->status !!}</span>
                                    </td>
                                    <td data-column="start" data-priority="5" data-label="{{__('encounter.day')}}">
                                        <span class="cell-content">{{ \Carbon\Carbon::parse($dato->start)->format('d-m-Y') }}</span>
                                    </td>
                                    <td data-column="time" data-priority="6" data-label="{{__('encounter.time')}}">
                                        <span class="cell-content">{{ $dato->time }}</span>
                                    </td>
                                    <td data-column="acciones" data-priority="1" data-label="{{__('consultation.data_table.actions')}}" class="text-end">
                                        <div class="btn-group btn-group-sm">
                                                @if(auth()->user()->can('view',$dato))
                                                <a href="{{ route('consultation.view',$dato->id) }}" class="btn btn-info btn-sm" title="{{__('generic.detail')}}">
                                                    <i  class="fa-solid fa-eye m-r-5 text-white"></i>
                                                </a>
                                                @endif
                                                @if(auth()->user()->can('edit',$dato))
                                                <a href="{{ route('consultation.show',$dato->appointment_id) }}" class="btn btn-primary btn-sm" title="{{__('generic.edit')}}">
                                                    <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                </a>
                                                @endif
                                                <button type="button" wire:click="$dispatch('showStatusHistory', { encounterId: {{ $dato->id }} })" class="btn btn-warning btn-sm" title="Historial de Status">
                                                    <i class="fa-solid fa-history m-r-5"></i>
                                                </button>
                                                @can('consultations.download_resumen')
                                                <a href="{{ route('consultation.download_resumen',$dato->appointment_id) }}" target="_blank" class="btn btn-danger btn-sm"  title="{{__('consultation.download_resumen')}}">
                                                    <i  class="fa-solid fa-file-pdf m-r-5"></i>
                                                </a>
                                                @endcan
                                                @if($dato->invoice)
                                                    <a href="{{ route('invoice.download',$dato->invoice->id) }}" target="_blank" class="btn btn-success btn-sm" title="{{__('consultation.download_invoice')}}">
                                                        <i  class="fa-solid fa-file-pdf m-r-5"></i>
                                                    </a>
                                                @endif
                                        </div>
                                        {{--}}<div class="dropdown dropdown-action">
                                            <a href="javascript:;" class="action-icon dropdown-toggle"  data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item"  href="{{ route('consultation.view',$dato->id) }}">  <i  class="fa-solid fa-eye m-r-5"></i>
                                                    {{__('generic.detail')}}
                                                </a>
                                                @if(auth()->user()->can('edit',$dato))
                                                <a class="dropdown-item"  href="{{ route('consultation.show',$dato->appointment_id) }}">  <i  class="fa-solid fa-pen-to-square m-r-5"></i>
                                                    {{__('generic.edit')}}
                                                </a>
                                                @endif
                                                <a class="dropdown-item"  href="{{ route('consultation.download_resumen',$dato->appointment_id) }}" target="_blank">  <i  class="fa-solid fa-file-pdf m-r-5"></i>
                                                    {{__('consultation.download_resumen')}}
                                                </a>
                                                @if($dato->invoice)
                                                    <a class="dropdown-item"  href="{{ route('invoice.download',$dato->invoice->id) }}" target="_blank">  <i  class="fa-solid fa-file-pdf m-r-5"></i>
                                                        {{__('consultation.download_invoice')}}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>{{--}}
                                    </td>
                                </tr>
                                <!-- Hidden row for expanded details -->
                                <tr class="row-details d-none" data-parent-row="{{ $dato->id }}">
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

    <!-- Status History Modal -->
    <livewire:consultation.status-history-modal />
</div>
