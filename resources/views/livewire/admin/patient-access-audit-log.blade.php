<div>
    <x-app-layout>
        <div class="page-wrapper">
            <div class="content">
                <!-- Page Header -->
                @component('components.page-header')
                    @slot('title')
                        Patient Access Audit Log
                    @endslot
                    @slot('li_1')
                        Compliance Monitoring
                    @endslot
                @endcomponent
                <!-- /Page Header -->

                <!-- Statistics Cards -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-2">Total Accesses</p>
                                        <h3 class="mb-0">{{ $stats['total_accesses'] }}</h3>
                                    </div>
                                    <div class="avatar avatar-lg bg-light-primary rounded">
                                        <i class="feather icon-eye"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-2">Unique Patients</p>
                                        <h3 class="mb-0">{{ $stats['unique_patients'] }}</h3>
                                    </div>
                                    <div class="avatar avatar-lg bg-light-success rounded">
                                        <i class="feather icon-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-muted mb-2">Unique Users</p>
                                        <h3 class="mb-0">{{ $stats['unique_users'] }}</h3>
                                    </div>
                                    <div class="avatar avatar-lg bg-light-warning rounded">
                                        <i class="feather icon-user-check"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Card -->
                <div class="row mb-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Filters & Search</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Patient Name / ID</label>
                                        <input type="text"
                                            class="form-control"
                                            wire:model.live.debounce.500ms="patientSearch"
                                            placeholder="Search patient...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">User Name / Email</label>
                                        <input type="text"
                                            class="form-control"
                                            wire:model.live.debounce.500ms="userSearch"
                                            placeholder="Search user...">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Action Type</label>
                                        <select class="form-control" wire:model.live="actionType">
                                            <option value="">All Actions</option>
                                            @foreach ($actionTypes as $type)
                                                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">From Date</label>
                                        <input type="date"
                                            class="form-control"
                                            wire:model.live="dateFrom">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">To Date</label>
                                        <input type="date"
                                            class="form-control"
                                            wire:model.live="dateTo">
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-12">
                                        <button class="btn btn-secondary btn-sm"
                                            wire:click="clearFilters">
                                            <i class="feather icon-x"></i> Clear Filters
                                        </button>
                                        <button class="btn btn-success btn-sm float-end"
                                            wire:click="exportCsv">
                                            <i class="feather icon-download"></i> Export CSV
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Audit Log Table -->
                <div class="row">
                    <div class="col-12">
                        <div class="card card-table">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Access Log</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    @if ($logs->count() > 0)
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Timestamp</th>
                                                    <th>User</th>
                                                    <th>Patient</th>
                                                    <th>Action</th>
                                                    <th>Resource</th>
                                                    <th>IP Address</th>
                                                    <th>Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($logs as $log)
                                                    <tr>
                                                        <td>
                                                            <small>{{ $log->access_timestamp?->format('Y-m-d H:i:s') ?? 'N/A' }}</small>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div>
                                                                    <h6 class="mb-0">{{ $log->user?->full_name ?? 'Unknown' }}</h6>
                                                                    <small class="text-muted">{{ $log->user?->email ?? 'N/A' }}</small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <h6 class="mb-0">{{ $log->patient?->name ?? 'Unknown' }}</h6>
                                                            @if ($log->patient?->identifier)
                                                                <small class="text-muted">{{ $log->patient->identifier }}</small>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <span class="badge
                                                                @if ($log->action_type === 'view') bg-info
                                                                @elseif ($log->action_type === 'download') bg-success
                                                                @elseif ($log->action_type === 'export') bg-warning
                                                                @else bg-secondary
                                                                @endif">
                                                                {{ ucfirst($log->action_type) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <small>{{ ucfirst(str_replace('_', ' ', $log->resource_type)) }}</small>
                                                        </td>
                                                        <td>
                                                            <small>{{ $log->ip_address ?? 'N/A' }}</small>
                                                        </td>
                                                        <td>
                                                            @if ($log->metadata)
                                                                <small class="text-muted">
                                                                    @if (isset($log->metadata['filename']))
                                                                        {{ $log->metadata['filename'] }}
                                                                    @endif
                                                                </small>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>

                                        <!-- Pagination -->
                                        <div class="mt-4">
                                            {{ $logs->links() }}
                                        </div>
                                    @else
                                        <div class="alert alert-info alert-block" role="alert">
                                            <strong>No access logs found</strong>
                                            <p>Try adjusting your filters or date range.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </x-app-layout>
</div>
