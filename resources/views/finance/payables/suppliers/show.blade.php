<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Módulo Financiero
                @endslot
                @slot('li_1')
                    Detalle de Proveedor
                @endslot
            @endcomponent

            <div class="row">
                <!-- Columna Izquierda -->
                <div class="col-md-4">
                    <!-- Información General -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Información General</h5>

                            <div class="mb-3">
                                <label class="form-label text-muted">Razón Social</label>
                                <p class="mb-0 fw-bold">{{ $supplier->legal_name }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Nombre Comercial</label>
                                <p class="mb-0">{{ $supplier->commercial_name ?? 'N/A' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">RUC</label>
                                <p class="mb-0 fw-bold">{{ $supplier->ruc }}{{ $supplier->dv ? '-' . $supplier->dv : '' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Estado</label>
                                <p class="mb-0">
                                    @if($supplier->isActive())
                                        <span class="badge bg-success">Activo</span>
                                    @else
                                        <span class="badge bg-secondary">Inactivo</span>
                                    @endif
                                </p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Días de Crédito</label>
                                <p class="mb-0 fw-bold">{{ $supplier->credit_days ?? 0 }} días</p>
                            </div>

                            <hr class="my-3">

                            <div class="mb-3">
                                <label class="form-label text-muted">Creado Por</label>
                                <p class="mb-0">{{ $supplier->createdBy->name ?? 'N/A' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Fecha de Creación</label>
                                <p class="mb-0">{{ $supplier->created_at->format('d/m/Y H:i') }}</p>
                            </div>

                            @if($supplier->updated_at !== $supplier->created_at)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Última Actualización</label>
                                    <p class="mb-0">{{ $supplier->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Acciones -->
                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Acciones</h5>
                            <div class="d-grid gap-2">
                                @can('payables.suppliers.manage')
                                    <a href="{{ route('finance.payables.suppliers.edit', $supplier) }}" class="btn btn-primary">
                                        <i class="fas fa-edit me-2"></i>Editar
                                    </a>
                                @endcan
                                <a href="{{ route('finance.payables.suppliers.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha -->
                <div class="col-md-8">
                    <!-- Información de Contacto -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Información de Contacto</h5>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Teléfono</label>
                                    <p class="mb-0">
                                        @if($supplier->phone)
                                            <a href="tel:{{ $supplier->phone }}" class="fw-bold">{{ $supplier->phone }}</a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </p>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Email</label>
                                    <p class="mb-0">
                                        @if($supplier->email)
                                            <a href="mailto:{{ $supplier->email }}" class="fw-bold">{{ $supplier->email }}</a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Dirección</label>
                                <p class="mb-0">{{ $supplier->address ?? 'N/A' }}</p>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-muted">Persona de Contacto</label>
                                <p class="mb-0">{{ $supplier->contact_person ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Información Contable -->
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Información Contable</h5>

                            <div class="row">
                                <div class="col-12">
                                    <label class="form-label text-muted">Cuenta Contable Asociada (CxP)</label>
                                    @if($supplier->accountingAccount)
                                        <p class="mb-0 fw-bold">
                                            {{ $supplier->accountingAccount->code }} - {{ $supplier->accountingAccount->name }}
                                        </p>
                                    @else
                                        <p class="mb-0 text-muted">No hay cuenta contable asociada</p>
                                    @endif
                                </div>
                            </div>

                            <hr class="my-3">

                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Total Facturable</label>
                                    <p class="mb-0 fw-bold text-warning">
                                        B/. {{ number_format($supplier->getTotalPayable(), 2) }}
                                    </p>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label text-muted">Total de Facturas</label>
                                    <p class="mb-0 fw-bold">
                                        {{ $supplier->invoices()->count() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Últimas Facturas -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Últimas Facturas</h5>
                        </div>
                        <div class="card-body">
                            @if($supplier->invoices()->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover">
                                        <thead>
                                            <tr>
                                                <th>Número de Factura</th>
                                                <th>Fecha</th>
                                                <th>Monto</th>
                                                <th>Estado</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($supplier->invoices()->latest()->limit(10)->get() as $invoice)
                                                <tr>
                                                    <td class="fw-bold">{{ $invoice->invoice_number }}</td>
                                                    <td>{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                                                    <td class="fw-bold">B/. {{ number_format($invoice->total_amount, 2) }}</td>
                                                    <td>
                                                        @switch($invoice->status)
                                                            @case('draft')
                                                                <span class="badge bg-secondary">Borrador</span>
                                                                @break
                                                            @case('registered')
                                                                <span class="badge bg-info">Registrada</span>
                                                                @break
                                                            @case('approved')
                                                                <span class="badge bg-success">Aprobada</span>
                                                                @break
                                                            @case('partial')
                                                                <span class="badge bg-warning">Pagada Parcialmente</span>
                                                                @break
                                                            @case('paid')
                                                                <span class="badge bg-success">Pagada</span>
                                                                @break
                                                            @case('overdue')
                                                                <span class="badge bg-danger">Vencida</span>
                                                                @break
                                                            @case('cancelled')
                                                                <span class="badge bg-dark">Cancelada</span>
                                                                @break
                                                            @default
                                                                <span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span>
                                                        @endswitch
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('finance.payables.invoices.show', $invoice) }}"
                                                           class="btn btn-sm btn-outline-primary" title="Ver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @if($supplier->invoices()->count() > 10)
                                    <div class="text-center mt-3">
                                        <a href="{{ route('finance.payables.invoices.index') }}?supplier={{ $supplier->id }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Ver todas las facturas
                                        </a>
                                    </div>
                                @endif
                            @else
                                <p class="text-muted text-center mb-0">No hay facturas registradas para este proveedor</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
