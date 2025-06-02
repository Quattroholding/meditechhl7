<div class="max-w-7xl mx-auto p-6">
    @section('css')
        <link href="{{url('styles/calendar.css?time='.time())}}" rel="stylesheet" />
    @stop
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Directorio Médico</h1>
        <p class="text-gray-600">Encuentra profesionales de la salud registrados en nuestro sistema</p>
    </div>
    @include('partials.message')
    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Búsqueda -->
            <div class="md:col-span-2 input-block  local-forms">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    Buscar profesional
                </label>
                <div class="relative">

                    <input
                        type="text"
                        id="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Nombre, apellido o cedula..."
                        class="form-control"
                    >
                </div>
            </div>

            <!-- Especialidad -->
            <div class="input-block  local-forms">
                <label for="specialty" class="block text-sm font-medium text-gray-700 mb-2">
                    Especialidad
                </label>
                <select
                    id="specialty"
                    wire:model.live="selectedSpecialty"
                    class="form-control"
                >
                    <option value="">Todas las especialidades</option>
                    @foreach($specialties as $key=>$value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
            {{--}}
            <!-- Registros por página -->
            <div>
                <label for="perPage" class="block text-sm font-medium text-gray-700 mb-2">
                    Mostrar
                </label>
                <select
                    id="perPage"
                    wire:model.live="perPage"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="12">12 por página</option>
                    <option value="24">24 por página</option>
                    <option value="48">48 por página</option>
                </select>
            </div>
            {{--}}
        </div>
        {{--}}
        <!-- Opciones adicionales -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center">
                    <input
                        type="checkbox"
                        wire:model.live="showInactive"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                    >
                    <span class="ml-2 text-sm text-gray-600">Mostrar profesionales inactivos</span>
                </label>
            </div>

            @if($search || $selectedSpecialty)
                <button
                    wire:click="clearFilters"
                    class="text-sm text-blue-600 hover:text-blue-800 font-medium"
                >
                    Limpiar filtros
                </button>
            @endif
        </div>
        {{--}}
    </div>

    <!-- Resultados -->
    <div class="mb-4">
        <p class="text-sm text-gray-600">
            Mostrando {{ $practitioners->count() }} de {{ $practitioners->total() }} profesionales
        </p>
    </div>

    <!-- Grid de profesionales -->
    @if($practitioners->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($practitioners as $practitioner)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                    <!-- Header de la tarjeta -->
                    <div class="p-6 pb-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    {{ $practitioner->name }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-2">
                                    ID: {{ $practitioner->identifier }}
                                </p>

                                <!-- Estado -->
                                <div class="flex items-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $practitioner->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        {{ $practitioner->active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>
                            <!-- Género -->
                            @if($practitioner->gender)
                                <div class="ml-4">
                                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-full {{ $practitioner->gender === 'female' ? 'bg-pink-100 text-pink-600' : 'bg-blue-100 text-blue-600' }}">
                                         <span>
                                        @if($practitioner->avatar())
                                            <img width="56" height="56" src="{{url('storage/'.$this->avatar()->path)}}" class="rounded-circle m-r-5" alt="" style="display:inline-block;">
                                         @else
                                            <img width="56" height="56" src="{{url('assets/img/profiles/avatar-02.jpg')}}" class="rounded-circle m-r-5" alt="" style="display:inline-block;">
                                        @endif
                                           </span>
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <!-- Información de contacto -->
                    @if($practitioner->phone || $practitioner->email)
                        <div class="px-6 pb-4">
                            <div class="space-y-2">
                                @if($practitioner->phone)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ $practitioner->phone }}
                                    </div>
                                @endif

                                @if($practitioner->email)
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ $practitioner->email }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Calificaciones -->
                    @if($practitioner->qualifications->count() > 0)
                        <div class="border-t border-gray-200 px-6 py-4">
                            <h4 class="text-sm font-medium text-gray-900 mb-3">Especialidades</h4>
                            <div class="space-y-2">
                                @foreach($practitioner->qualifications->take(3) as $qualification)
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 truncate">
                                                {{ $qualification->medicalSpeciality->name }}
                                            </p>
                                            @if($qualification->issuer_name)
                                                <p class="text-xs text-gray-500 truncate">
                                                    {{ $qualification->issuer_name }}
                                                </p>
                                            @endif
                                        </div>
                                        {{--}}
                                        <div class="ml-2 flex-shrink-0">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs {{ $qualification->is_valid ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $qualification->is_valid ? 'Vigente' : 'Vencida' }}
                                            </span>
                                        </div>
                                        {{--}}
                                    </div>
                                @endforeach

                                @if($practitioner->qualifications->count() > 3)
                                    <p class="text-xs text-gray-500 mt-2">
                                        +{{ $practitioner->qualifications->count() - 3 }} calificaciones más
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Acciones -->
                    <div class="bg-gray-50 px-6 py-3 rounded-b-lg">
                        <button class="btn btn-primary" wire:click="requestAppointment({{$practitioner->id}})">
                          <i class="fa fa-calendar"></i> {{__('patient.request_appointment')}}
                        </button>
                    </div>

                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        <div class="flex justify-center">
            {{ $practitioners->links() }}
        </div>
    @else
        <!-- Estado vacío -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No se encontraron profesionales</h3>
            <p class="mt-2 text-gray-500">
                @if($search || $selectedSpecialty)
                    Intenta ajustar los filtros de búsqueda.
                @else
                    No hay profesionales registrados en el sistema.
                @endif
            </p>
            @if($search || $selectedSpecialty)
                <button
                    wire:click="clearFilters"
                    class="mt-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 transition-colors duration-200"
                >
                    Limpiar filtros
                </button>
            @endif
        </div>
    @endif

    <!-- Loading state -->
    <div wire:loading.delay wire:target="search,selectedSpecialty,perPage,showInactive" class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700">Cargando...</span>
            </div>
        </div>
    </div>
    <!-- Modal -->
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">{{__('patient.request_appointment')}} : {{$doctor_name}}</h2>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <form wire:submit="saveAppointment">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="input-block local-forms">
                            <x-input-label for="Fecha" :value="__('appointment.date')" required/>
                            <input wire:model="appointment_date" type="date" class="form-control-full" required min="{{now()->addWeek(1)->format('Y-m-d')}}">
                            @error('appointment_date') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                        <div class="input-block local-forms">
                            <x-input-label for="appointment_time" :value="__('appointment.time')" required/>
                            <input wire:model="appointment_time" type="time" class="form-control-full" required>
                            @error('appointment_time') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div  class="input-block local-forms">
                        <x-input-label class="form-label" required>{{__('appointment.duration')}}</x-input-label>
                        <select wire:model="duration" class="form-control-full">
                            <option value="15">15 minutos</option>
                            <option value="30">30 minutos</option>
                            <option value="45">45 minutos</option>
                            <option value="60">60 minutos</option>
                            <option value="90">90 minutos</option>
                        </select>
                        @error('duration') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-block local-forms">
                        <x-input-label for="consulting_room_id" :value="__('appointment.consulting_room')" required/>
                        <x-select-input  wire:model="consulting_room_id" name="consulting_room_id" :options="$consultorios"  class="block w-full"/>
                        @error('consulting_room_id') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-block local-forms">
                        <x-input-label for="medical_speciality_id" :value="__('appointment.speciality')" required/>
                        <x-select-input wire:model="medical_speciality_id" id="medical_speciality_id" name="medical_speciality_id" :options="$especialidades"  class="block w-full"/>
                        @error('medical_speciality_id') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-block local-forms">
                        <x-input-label for="service_type" :value="__('appointment.service_type')" required/>
                        <x-text-input wire:model="service_type" id="service_type" class="block mt-1 w-full" type="text" name="service_type" placeholder="EJ:Consulta Especializada"/>
                        @error('service_type') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div class="input-block local-forms">
                        <label class="form-label">{{__('appointment.reason')}}</label>
                        <textarea wire:model="description" class="form-control-full" rows="3" placeholder="Describir el motivo de la consulta">{{$description}}</textarea>
                        @error('description') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    <div style="margin-top: 30px; display: flex; gap: 15px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            {{__('patient.request_appointment')}}
                        </button>
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastr', (event) => {
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                });
            });
        });
    </script>
</div>
