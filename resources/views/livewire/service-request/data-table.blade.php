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

                        @endslot
                        @slot('show_create', false)
                    @endcomponent
                    <!-- /Table Header -->

                    {{-- <livewire:service-request.modal-save wire:model="showModal"/> --}}

                    <!-- Modals -->
                    <livewire:service-request.upload-result />
                    <livewire:service-request.view-results />
                    <livewire:service-request.status-manager />

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                            <tr>
                                <th data-column="id" data-priority="1">
                                    <x-table-sort-button title="{{ __('service_request.id') }}" columnName="id" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="code" data-priority="2">
                                    <x-table-sort-button title="{{ __('service_request.code') }}" columnName="code" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="type" data-priority="2">
                                    <x-table-sort-button title="{{ __('service_request.type') }}" columnName="type" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="code_display" data-priority="3">
                                    <x-table-sort-button title="{{ __('service_request.description') }}" columnName="code_display" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="patient" data-priority="4">
                                    <x-table-sort-button title="{{ __('service_request.patient') }}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="practitioner" data-priority="5">
                                    <x-table-sort-button title="{{ __('service_request.practitioner') }}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="status" data-priority="6">
                                    <x-table-sort-button title="{{ __('service_request.status') }}" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                {{--}}
                                <th data-column="priority" data-priority="7">
                                    <x-table-sort-button title="{{ __('service_request.priority') }}" columnName="priority" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                {{--}}
                                <th data-column="occurrence_start" data-priority="8">
                                    <x-table-sort-button title="{{ __('service_request.occurrence_start') }}" columnName="occurrence_start" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                                <th data-column="acciones" data-priority="1" class="text-end">
                                    <x-table-sort-button title="{{ __('service_request.actions') }}" columnName="" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $serviceRequest)
                                <tr class="table-row" data-row-id="{{ $serviceRequest->id }}">
                                    <td data-column="id" data-priority="1" data-label="{{ __('service_request.id') }}">
                                        <span class="row-expand-btn d-none me-2" onclick="toggleRowDetails(this)">
                                            <i class="fas fa-plus-circle text-primary" style="cursor: pointer;"></i>
                                        </span>
                                        <span class="cell-content">{{ $serviceRequest->id }}</span>
                                    </td>
                                    <td data-column="code" data-priority="2" data-label="{{ __('service_request.code') }}">
                                        <span class="cell-content">{{ $serviceRequest->code }}</span>
                                    </td>
                                    <td data-column="type" data-priority="2" data-label="{{ __('service_request.type') }}">
                                        <span class="cell-content">{{ $serviceRequest->service_type }}</span>
                                    </td>
                                    <td data-column="code_display" data-priority="3" data-label="{{ __('service_request.description') }}">
                                        <span class="cell-content">
                                            @php
                                                $fullDescription = $serviceRequest->cpt?->description_es ?? $serviceRequest->code_display ?? __('service_request.no_data');
                                                $shortDescription = strlen($fullDescription) > 30 ? substr($fullDescription, 0, 30) . '...' : $fullDescription;
                                            @endphp
                                            <span
                                                class="description-text {{ strlen($fullDescription) > 30 ? 'cursor-pointer text-primary' : '' }}"
                                                @if(strlen($fullDescription) > 30)
                                                    title="{{ $fullDescription }}"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    onclick="showFullDescription('{{ addslashes($fullDescription) }}')"
                                                @endif
                                            >
                                                {{ $shortDescription }}
                                            </span>
                                        </span>
                                    </td>
                                    <td data-column="patient" data-priority="4" data-label="{{ __('service_request.patient') }}">
                                        <span class="cell-content">
                                            @if($serviceRequest->patient)
                                                {{ $serviceRequest->patient->name }}
                                            @else
                                                {{ __('service_request.no_data') }}
                                            @endif
                                        </span>
                                    </td>
                                    <td data-column="practitioner" data-priority="5" data-label="{{ __('service_request.practitioner') }}">
                                        <span class="cell-content">
                                            @if($serviceRequest->practitioner)
                                                {{ $serviceRequest->practitioner->name }}
                                            @else
                                                {{ __('service_request.no_data') }}
                                            @endif
                                        </span>
                                    </td>
                                    <td data-column="status" data-priority="6" data-label="{{ __('service_request.status') }}">
                                        <span class="cell-content">
                                            @php
                                                $statusColor = match($serviceRequest->status) {
                                                    'draft' => 'secondary',
                                                    'active' => 'primary',
                                                    'on-hold' => 'warning',
                                                    'revoked' => 'danger',
                                                    'completed' => 'success',
                                                    'entered-in-error' => 'dark',
                                                    'unknown' => 'light',
                                                    default => 'secondary'
                                                };
                                                $statusText = match($serviceRequest->status) {
                                                    'active' => __('service_request.status_active'),
                                                    'completed' => __('service_request.status_completed'),
                                                    'cancelled' => __('service_request.status_cancelled'),
                                                    'on-hold' => __('service_request.status_on_hold'),
                                                    'draft' => __('service_request.status_draft'),
                                                    'entered-in-error' => __('service_request.status_entered_in_error'),
                                                    default => ucfirst($serviceRequest->status)
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $statusColor }}">
                                                {{ $statusText }}
                                            </span>
                                        </span>
                                    </td>
                                    {{--}}
                                    <td data-column="priority" data-priority="7" data-label="{{ __('service_request.priority') }}">
                                        <span class="cell-content">
                                            @php
                                                $priorityColor = match($serviceRequest->priority) {
                                                    'urgent' => 'danger',
                                                    'asap' => 'warning',
                                                    'routine' => 'success',
                                                    'stat' => 'danger',
                                                    default => 'secondary'
                                                };
                                                $priorityText = match($serviceRequest->priority) {
                                                    'urgent' => __('service_request.priority_urgent'),
                                                    'asap' => __('service_request.priority_asap'),
                                                    'routine' => __('service_request.priority_routine'),
                                                    'stat' => __('service_request.priority_stat'),
                                                    default => ucfirst($serviceRequest->priority ?? __('service_request.not_available'))
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $priorityColor }}">
                                                {{ $priorityText }}
                                            </span>
                                        </span>
                                    </td>
                                    {{--}}
                                    <td data-column="occurrence_start" data-priority="8" data-label="{{ __('service_request.occurrence_start') }}">
                                        <span class="cell-content">
                                            {{ $serviceRequest->occurrence_start ? \Carbon\Carbon::parse($serviceRequest->occurrence_start)->format('d/m/Y') : __('service_request.no_data') }}
                                        </span>
                                    </td>
                                    <td data-column="acciones" data-priority="1" data-label="{{ __('service_request.actions') }}" class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <button
                                                type="button"
                                                class="btn btn-secondary btn-sm d-flex align-items-center"
                                                title="{{ __('service_request_result.view') }} ({{ $serviceRequest->results_count }} {{ __('service_request_result.results_count_short') }})"
                                                wire:click="openViewResultsModal({{ $serviceRequest->id }})"
                                            >
                                                <i class="fa-solid fa-eye"></i>
                                                    <span class="ms-1 results-count-inline">{{ $serviceRequest->results_count }}</span>

                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-warning btn-sm"
                                                title="{{ __('service_request.change_status') }}"
                                                wire:click="openStatusModal({{ $serviceRequest->id }})"
                                            >
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                            <button
                                                type="button"
                                                class="btn btn-primary btn-sm"
                                                title="{{ __('service_request_result.upload_new') }}"
                                                wire:click="openUploadModal({{ $serviceRequest->id }})"
                                            >
                                                <i class="fas fa-upload m-r-5"></i>
                                            </button>
                                            {{-- <a href="javascript:;" onclick="confirm('{{ __('service_request.delete_confirmation') }}') || event.stopImmediatePropagation()" wire:click="deleteServiceRequest({{ $serviceRequest->id }})" class="btn btn-danger btn-sm" title="{{ __('service_request.delete') }}">
                                                <i class="fa fa-trash-alt m-r-5"></i>
                                            </a> --}}
                                        </div>
                                    </td>
                                </tr>
                                <!-- Hidden row for expanded details -->
                                <tr class="row-details d-none" data-parent-row="{{ $serviceRequest->id }}">
                                    <td colspan="9" class="p-3 bg-light">
                                        <div class="row-details-content">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>{{ __('service_request.service_type') }}:</strong> {{ $serviceRequest->service_type }}<br>
                                                    <strong>{{ __('service_request.intent') }}:</strong> {{ $serviceRequest->intent }}<br>
                                                    <strong>{{ __('service_request.do_not_perform') }}:</strong> {{ $serviceRequest->do_not_perform ? __('service_request.yes') : __('service_request.no') }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>{{ __('service_request.quantity') }}:</strong> {{ $serviceRequest->quantity }} {{ $serviceRequest->quantity_unit }}<br>
                                                    <strong>{{ __('service_request.occurrence_end') }}:</strong> {{ $serviceRequest->occurrence_end ? $serviceRequest->occurrence_end->format('d/m/Y') : __('service_request.no_data') }}<br>
                                                    <strong>{{ __('service_request.created') }}:</strong> {{ $serviceRequest->authored_on ? $serviceRequest->authored_on->format('d/m/Y H:i') : __('service_request.no_data') }}
                                                </div>
                                            </div>
                                            @if($serviceRequest->note)
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <strong>{{ __('service_request.notes') }}:</strong><br>
                                                        {{ $serviceRequest->note }}
                                                    </div>
                                                </div>
                                            @endif
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

    <!-- Modal para mostrar descripción completa -->
    <div class="modal fade" id="descriptionModal" tabindex="-1" aria-labelledby="descriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel">{{ __('service_request.description_modal_title') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="fullDescriptionContent"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('service_request.close') }}</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showFullDescription(description) {
            document.getElementById('fullDescriptionContent').textContent = description;
            var modal = new bootstrap.Modal(document.getElementById('descriptionModal'));
            modal.show();
        }

        // Inicializar tooltips de Bootstrap
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
        .description-text:hover {
            text-decoration: underline;
        }
        .results-count-inline {
            font-size: 0.75em;
            font-weight: bold;
            color: rgba(255, 255, 255, 0.9);
            background-color: rgba(255, 255, 255, 0.2);
            padding: 1px 4px;
            border-radius: 2px;
            min-width: 16px;
            text-align: center;
            line-height: 1;
        }
    </style>
</div>


