<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Aseguradoras
                @endslot
                @slot('li_1')
                    Detalles de Aseguradora
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-heading">
                                        <h4>{{ $insurance->name }}</h4>
                                        <span class="badge {{ $insurance->is_active ? 'bg-success' : 'bg-danger' }} mb-3">
                                            {{ $insurance->is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group local-forms">
                                        <label><strong>Código:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->code }}</p>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group local-forms">
                                        <label><strong>Email:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->email ?? 'No especificado' }}</p>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group local-forms">
                                        <label><strong>Teléfono:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->phone ?? 'No especificado' }}</p>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group local-forms">
                                        <label><strong>Persona de Contacto:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->contact_person ?? 'No especificado' }}</p>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group local-forms">
                                        <label><strong>Email de Contacto:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->contact_email ?? 'No especificado' }}</p>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group local-forms">
                                        <label><strong>Teléfono de Contacto:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->contact_phone ?? 'No especificado' }}</p>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group local-forms">
                                        <label><strong>% Cobertura por Defecto:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->default_coverage_percentage ?? 0 }}%</p>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group local-forms">
                                        <label><strong>Monto Copago por Defecto:</strong></label>
                                        <p class="form-control-plaintext">${{ number_format($insurance->default_copay_amount ?? 0, 2) }}</p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group local-forms">
                                        <label><strong>Dirección:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->address ?? 'No especificada' }}</p>
                                    </div>
                                </div>

                                @if($insurance->notes)
                                <div class="col-12">
                                    <div class="form-group local-forms">
                                        <label><strong>Notas:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->notes }}</p>
                                    </div>
                                </div>
                                @endif

                                <div class="col-12 col-md-6">
                                    <div class="form-group local-forms">
                                        <label><strong>Fecha de Creación:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->created_at }}</p>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group local-forms">
                                        <label><strong>Última Actualización:</strong></label>
                                        <p class="form-control-plaintext">{{ $insurance->updated_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="doctor-submit text-end">
                                        <a href="{{ route('insurances.edit', $insurance) }}" class="btn btn-primary me-2">Editar</a>
                                        <a href="{{ route('insurances.index') }}" class="btn btn-primary cancel-form">Volver al Listado</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
