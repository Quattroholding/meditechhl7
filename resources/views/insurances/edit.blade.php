<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Aseguradoras
                @endslot
                @slot('li_1')
                    Editar Aseguradora
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-12">
                                <div class="form-heading">
                                    <h4>Editar Aseguradora: {{ $insurance->name }}</h4>
                                </div>
                            </div>

                            <form action="{{ route('insurances.update', $insurance) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>Nombre <span class="login-danger">*</span></label>
                                            <input class="form-control @error('name') is-invalid @enderror" 
                                                   type="text" name="name" value="{{ old('name', $insurance->name) }}" 
                                                   placeholder="Nombre de la aseguradora" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>Código <span class="login-danger">*</span></label>
                                            <input class="form-control @error('code') is-invalid @enderror" 
                                                   type="text" name="code" value="{{ old('code', $insurance->code) }}" 
                                                   placeholder="Código único" required>
                                            @error('code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>Email</label>
                                            <input class="form-control @error('email') is-invalid @enderror" 
                                                   type="email" name="email" value="{{ old('email', $insurance->email) }}" 
                                                   placeholder="correo@aseguradora.com">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>Teléfono</label>
                                            <input class="form-control @error('phone') is-invalid @enderror" 
                                                   type="text" name="phone" value="{{ old('phone', $insurance->phone) }}" 
                                                   placeholder="Teléfono principal">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-group local-forms">
                                            <label>Dirección</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      name="address" rows="3" 
                                                      placeholder="Dirección de la aseguradora">{{ old('address', $insurance->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>Persona de Contacto</label>
                                            <input class="form-control @error('contact_person') is-invalid @enderror" 
                                                   type="text" name="contact_person" value="{{ old('contact_person', $insurance->contact_person) }}" 
                                                   placeholder="Nombre del contacto">
                                            @error('contact_person')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>Email de Contacto</label>
                                            <input class="form-control @error('contact_email') is-invalid @enderror" 
                                                   type="email" name="contact_email" value="{{ old('contact_email', $insurance->contact_email) }}" 
                                                   placeholder="contacto@aseguradora.com">
                                            @error('contact_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>Teléfono de Contacto</label>
                                            <input class="form-control @error('contact_phone') is-invalid @enderror" 
                                                   type="text" name="contact_phone" value="{{ old('contact_phone', $insurance->contact_phone) }}" 
                                                   placeholder="Teléfono del contacto">
                                            @error('contact_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>% Cobertura por Defecto</label>
                                            <input class="form-control @error('default_coverage_percentage') is-invalid @enderror" 
                                                   type="number" name="default_coverage_percentage" 
                                                   value="{{ old('default_coverage_percentage', $insurance->default_coverage_percentage) }}" 
                                                   placeholder="0.00" min="0" max="100" step="0.01">
                                            @error('default_coverage_percentage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>Monto Copago por Defecto</label>
                                            <input class="form-control @error('default_copay_amount') is-invalid @enderror" 
                                                   type="number" name="default_copay_amount" 
                                                   value="{{ old('default_copay_amount', $insurance->default_copay_amount) }}" 
                                                   placeholder="0.00" min="0" step="0.01">
                                            @error('default_copay_amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12 col-md-6 col-xl-6">
                                        <div class="form-group local-forms">
                                            <label>Estado <span class="login-danger">*</span></label>
                                            <select class="form-control select @error('is_active') is-invalid @enderror" name="is_active" required>
                                                <option value="">Seleccionar Estado</option>
                                                <option value="1" {{ old('is_active', $insurance->is_active) == 1 ? 'selected' : '' }}>Activo</option>
                                                <option value="0" {{ old('is_active', $insurance->is_active) == 0 ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                            @error('is_active')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="form-group local-forms">
                                            <label>Notas</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                      name="notes" rows="3" 
                                                      placeholder="Notas adicionales sobre la aseguradora">{{ old('notes', $insurance->notes) }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-12">
                                        <div class="doctor-submit text-end">
                                            <button type="submit" class="btn btn-primary submit-form me-2">Actualizar</button>
                                            <a href="{{ route('insurances.index') }}" class="btn btn-primary cancel-form">Cancelar</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>