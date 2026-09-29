<div wire:poll="5s">
    <div class="row">
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => true])
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Estado') }}</label>
                                    <select wire:model.live="statusFilter" name="status" class="form-select">
                                        @foreach ($statusOptions as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Buscar') }}</label>
                                    <input
                                        type="text"
                                        wire:model.live="search"
                                        placeholder="Nombre de archivo..."
                                        class="form-control"
                                    />
                                </div>
                            </div>
                        @endslot
                        @slot('title')
                            Gestión de Documentos
                        @endslot
                        @slot('li_1')
                            {{ route('documents.index') }}
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                    @include('partials.message')

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                                <tr>
                                    <th data-column="archivo" data-priority="1">
                                        <x-table-sort-button title="Archivo" columnName="original_filename" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                    </th>
                                    <th data-column="tipo" data-priority="2">
                                        <x-table-sort-button title="Tipo" columnName=""/>
                                    </th>
                                    <th data-column="estado" data-priority="3">
                                        <x-table-sort-button title="Estado" columnName=""/>
                                    </th>
                                    <th data-column="confianza" data-priority="4">
                                        <x-table-sort-button title="Confianza" columnName=""/>
                                    </th>
                                    <th data-column="subido_por" data-priority="5">
                                        <x-table-sort-button title="Subido Por" columnName=""/>
                                    </th>
                                    <th data-column="fecha" data-priority="6">
                                        <x-table-sort-button title="Fecha" columnName="created_at" :sortField="$sortField" :sortDirection="$sortDirection"/>
                                    </th>
                                    <th data-column="acciones" data-priority="7" class="text-end">
                                        <x-table-sort-button title="Acciones" columnName=""/>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($documents as $document)
                                    <tr class="table-row" data-row-id="{{ $document->id }}">
                                        <td data-column="archivo" data-priority="1" data-label="Archivo">
                                            <span class="cell-content">
                                                <i class="fa fa-file-pdf text-danger me-2"></i>
                                                {{ Str::limit($document->original_filename, 25) }}
                                            </span>
                                        </td>
                                        <td data-column="tipo" data-priority="2" data-label="Tipo">
                                            <span class="cell-content">
                                                @if ($document->document_type)
                                                    {{ $document->document_type->label() }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </td>
                                        <td data-column="estado" data-priority="3" data-label="Estado">
                                            <span class="badge
                                                @if ($document->status->value === 'pending_parsing') bg-warning
                                                @elseif ($document->status->value === 'parsing') bg-info
                                                @elseif ($document->status->value === 'parsing_failed') bg-danger
                                                @elseif ($document->status->value === 'parsed') bg-primary
                                                @elseif ($document->status->value === 'approved') bg-success
                                                @elseif ($document->status->value === 'processing') bg-secondary
                                                @elseif ($document->status->value === 'processed') bg-success
                                                @elseif ($document->status->value === 'rejected') bg-danger
                                                @else bg-secondary
                                                @endif
                                            ">
                                                {{ $document->status->label() }}
                                            </span>
                                        </td>
                                        <td data-column="confianza" data-priority="4" data-label="Confianza">
                                            <span class="cell-content">
                                                @if ($document->parseResult && $document->parseResult->confidence_score)
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="progress" style="width: 100px; height: 6px;">
                                                            <div
                                                                class="progress-bar
                                                                @if ($document->parseResult->confidence_score >= 0.8) bg-success
                                                                @elseif ($document->parseResult->confidence_score >= 0.6) bg-warning
                                                                @else bg-danger
                                                                @endif"
                                                                role="progressbar"
                                                                style="width: {{ ($document->parseResult->confidence_score * 100) }}%"
                                                            ></div>
                                                        </div>
                                                        <small>{{ round($document->parseResult->confidence_score * 100) }}%</small>
                                                    </div>
                                                @else
                                                    <span class="cell-content text-muted">-</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td data-column="subido_por" data-priority="5" data-label="Subido Por">
                                            <span class="cell-content">
                                                @if ($document->uploadedByUser)
                                                    {{ $document->uploadedByUser->name }}
                                                @else
                                                    -
                                                @endif
                                            </span>
                                        </td>
                                        <td data-column="fecha" data-priority="6" data-label="Fecha">
                                            <span class="cell-content">{{ $document->created_at->format('d-m-Y H:i') }}</span>
                                        </td>
                                        <td data-column="acciones" data-priority="7" data-label="Acciones" class="text-end">
                                            <div class="btn-group btn-group-sm">
                                                @if ($document->status->value === 'parsed')
                                                    <a href="{{ route('documents.detail', $document) }}" class="btn btn-primary btn-sm" title="Revisar documento">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </a>
                                                @elseif ($document->status->value === 'approved' || $document->status->value === 'processing' || $document->status->value === 'processed')
                                                    <a href="{{ route('documents.detail', $document) }}" class="btn btn-success btn-sm" title="Ver detalles">
                                                        <i class="fa-solid fa-check"></i>
                                                    </a>
                                                @elseif ($document->status->value === 'parsing')
                                                    <span class="text-info">
                                                        <i class="fa fa-spinner fa-spin me-2"></i>
                                                        <small>Procesando</small>
                                                    </span>
                                                @elseif ($document->status->value === 'parsing_failed')
                                                    <span class="text-danger">
                                                        <i class="fa fa-exclamation-circle"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-4 text-center text-muted">
                                            <i class="fa fa-inbox fa-2x mb-2 d-block"></i>
                                            No hay documentos para mostrar
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @include('partials.pagination', ['data' => $documents])
                </div>
            </div>
        </div>
    </div>
</div>
