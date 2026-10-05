<div class="page-wrapper">
    <div class="row content">
        @component('components.page-header')
            @slot('title')
                Asientos Contables
            @endslot
            @slot('li_1')
                Contabilidad
            @endslot
        @endcomponent
        <div class="col-sm-12">
            <div class="card card-table show-entire">
                <div class="card-body">
                    <!-- Table Header -->
                    @component('components.table-header', ['show_create' => auth()->user()->can('accounting.entries.create'), 'title' => '', 'li_1' => route('accounting.journal-entries.create')])
                        @slot('filters')
                            <div class="d-flex flex-wrap gap-2">
                                <div class="input-block local-forms mb-0">
                                    <label>{{ __('Estatus') }}</label>
                                    <x-select-input wire:model.live="statusFilter" id="statusFilter" name="status" :options="['draft' => 'Borrador', 'posted' => 'Contabilizado', 'reversed' => 'Revertido']" :selected="[]" class="form-select" />
                                </div>
                            </div>
                        @endslot
                    @endcomponent
                    <!-- /Table Header -->

                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0 responsive-table">
                            <thead>
                                <tr>
                                    <th data-column="entry_number" data-priority="1">
                                        <x-table-sort-button title="Número" columnName="entry_number" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="entry_date" data-priority="2">
                                        <x-table-sort-button title="Fecha" columnName="entry_date" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="description" data-priority="3">
                                        <x-table-sort-button title="Descripción" columnName="description" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="document_type" data-priority="4">
                                        <x-table-sort-button title="Tipo Documento" columnName="document_type" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    <th data-column="total" data-priority="5">
                                        <x-table-sort-button title="Total" columnName="" />
                                    </th>
                                    <th data-column="status" data-priority="6">
                                        <x-table-sort-button title="Estatus" columnName="status" :sortField="$sortField" :sortDirection="$sortDirection" />
                                    </th>
                                    @canany(['accounting.entries.create', 'accounting.entries.post', 'accounting.entries.reverse'])
                                        <th data-column="acciones" data-priority="1" class="text-end">
                                            <x-table-sort-button title="{{ __('Acciones') }}" columnName="" />
                                        </th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $entry)
                                    <tr class="table-row" data-row-id="{{ $entry->id }}">
                                        <td data-column="entry_number" data-priority="1" data-label="Número">
                                            <span class="cell-content">{{ $entry->entry_number }}</span>
                                        </td>
                                        <td data-column="entry_date" data-priority="2" data-label="Fecha">
                                            <span class="cell-content">{{ \Carbon\Carbon::parse($entry->entry_date)->format('d-m-Y') }}</span>
                                        </td>
                                        <td data-column="description" data-priority="3" data-label="Descripción">
                                            <span class="cell-content">{{ \Illuminate\Support\Str::limit($entry->description, 50) }}</span>
                                        </td>
                                        <td data-column="document_type" data-priority="4" data-label="Tipo Documento">
                                            <span class="cell-content badge bg-info">{{ $entry->document_type }}</span>
                                        </td>
                                        <td data-column="total" data-priority="5" data-label="Total">
                                            <span class="cell-content">
                                                B/. {{ number_format($entry->journalEntryLines->sum('debit'), 2) }}
                                            </span>
                                        </td>
                                        <td data-column="status" data-priority="6" data-label="Estatus">
                                            <span class="cell-content badge me-1
                                                {{ $entry->status === 'draft' ? 'bg-warning' : ($entry->status === 'posted' ? 'bg-success' : 'bg-danger') }}">
                                                {{ ucfirst($entry->status) }}
                                            </span>
                                        </td>
                                        @canany(['accounting.entries.create', 'accounting.entries.post', 'accounting.entries.reverse'])
                                            <td data-column="acciones" data-priority="1" data-label="{{ __('Acciones') }}" class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('accounting.journal-entries.show', $entry->id) }}" class="btn btn-info btn-sm" title="Ver">
                                                        <i class="fa-solid fa-eye m-r-5"></i>
                                                    </a>
                                                    @if($entry->status === 'draft' && auth()->user()->can('accounting.entries.create'))
                                                        <a href="{{ route('accounting.journal-entries.edit', $entry->id) }}" class="btn btn-success btn-sm" title="Editar">
                                                            <i class="fa-solid fa-pen-to-square m-r-5"></i>
                                                        </a>
                                                    @endif
                                                    @if($entry->status === 'posted' && auth()->user()->can('accounting.entries.reverse'))
                                                        <button wire:click="reverseEntry({{ $entry->id }})" class="btn btn-danger btn-sm" title="Revertir">
                                                            <i class="fa-solid fa-undo m-r-5"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        @endcanany
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay asientos registrados
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @include('partials.pagination', ['data' => $data])
                </div>
            </div>
        </div>
    </div>
</div>
