<div class="medical-calendar-container">
    @section('css')
        <link href="{{url('styles/calendar.css?time='.time())}}" rel="stylesheet" />
    @stop
    <!-- Header -->
    <div class="calendar-header">
        <div class="header-controls">
            <div class="view-buttons">
                <button wire:click="changeView('monthly')" class="btn {{ $currentView === 'monthly' ? 'btn-primary active' : 'btn-secondary' }}">
                    Mensual
                </button>
                <button wire:click="changeView('weekly')" class="btn {{ $currentView === 'weekly' ? 'btn-primary active' : 'btn-secondary' }}">
                    Semanal
                </button>
                <button wire:click="changeView('daily')" class="btn {{ $currentView === 'daily' ? 'btn-primary active' : 'btn-secondary' }}">
                    Diaria
                </button>
            </div>

            <div class="navigation">
                <button wire:click="navigateCalendar(-1)" class="nav-btn">‹</button>
                <div class="current-period">{{ $currentPeriod }}</div>
                <button wire:click="navigateCalendar(1)" class="nav-btn">›</button>
                <button wire:click="goToToday" class="btn btn-secondary" style="margin-left: 10px;">Hoy</button>
            </div>

            <button wire:click="openModal" class="btn btn-primary">+ Nueva Cita</button>
        </div>
        <!-- Estadísticas -->
        {{--}}
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-number">{{ $stats['total'] ?? 0 }}</span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">{{ $stats['confirmed'] ?? 0 }}</span>
                <span class="stat-label">Confirmadas</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">{{ $stats['fullfilled'] ?? 0 }}</span>
                <span class="stat-label">Completadas</span>
            </div>
            <div class="stat-card">
                <span class="stat-number">{{ $stats['today'] ?? 0 }}</span>
                <span class="stat-label">Hoy</span>
            </div>
        </div>
        {{--}}
        <!-- Filtros -->
        <div class="filters-section">
        <div>
            <input wire:model.live="searchTerm" type="text" placeholder="Buscar paciente, doctor..." class="form-control">
        </div>
        <div>
            <select wire:model.live="selectedDoctor" class="form-control">
                <option value="">Todos los doctores</option>
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor['id'] }}">{{ $doctor['name'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select wire:model.live="selectedStatus" class="form-control">
                <option value="">Todos los estados</option>
                <option value="booked">Programada</option>
                <option value="arrived">Llegada</option>
                <option value="checked-in">En Progreso</option>
                <option value="fullfilled">Completada</option>
                <option value="cancelled">Cancelada</option>
                <option value="noshow">No Asistió</option>
            </select>
        </div>
        <div>
            <button wire:click="clearFilters" class="btn btn-secondary">Limpiar Filtros</button>
        </div>
        {{--}}
        <div>
            <button wire:click="exportFHIR" class="btn btn-secondary">Exportar FHIR</button>
        </div>
        {{--}}
        <div class="text-end">
            <button wire:click="toggleTimeBlockConfig" class="btn btn-secondary">
                ⚙️ Configurar Bloques
            </button>
        </div>

    </div>

        <!-- NUEVA SECCIÓN: Configuración de Bloques de Tiempo -->
        <div>

        </div>
    </div>

    @include('partials.message')


    <!-- Calendario -->
    <div class="calendar-content">

        @if(in_array($currentView ,['monthly','weekly']))
        <div class="legend">

            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #28a745, #20c997);"></div>
                <span>Programada</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #007bff, #6610f2);"></div>
                <span>Llegada</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #dedede, #ededed);"></div>
                <span>Pendiente de Confirmación</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #ffc107, #fd7e14);"></div>
                <span>En Progreso</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #6c757d, #495057);"></div>
                <span>Completada</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #dc3545, #e83e8c);"></div>
                <span>Cancelada</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #6f42c1, #e83e8c);"></div>
                <span>No Asistió</span>
            </div>
        </div>
        @endif
        @if($currentView === 'monthly')
            @include('appointments.calendar.montly-view',array('calendarData'=>$calendarData))
            {{--}}
            <livewire:appointment.calendar-montly-view :calendarData="$calendarData"/>
            {{--}}
        @elseif($currentView === 'weekly')
            @include('appointments.calendar.weekly-view',array('calendarData'=>$calendarData))
            {{--}}
            <livewire:appointment.calendar-weekly-view :calendarData="$calendarData"/>
            {{--}}
        @elseif($currentView === 'daily')
            @include('appointments.calendar.daily-timeline-view')
            {{--}}
                @include('appointments.calendar.daily-view',array('calendarData'=>$calendarData))
                <livewire:appointment.calendar-daily-view :calendarData="$calendarData"/>
           {{--}}
        @endif
    </div>
    <div class="my-3">&nbsp;</div>
    <!-- Modal -->
    @if($showModal)
        <div class="modal-overlay" wire:click="closeModal" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop>
                <div class="modal-header">
                    <h2 class="modal-title">{{ $modalTitle }}</h2>
                    <button wire:click="closeModal" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>
                <form wire:submit="saveAppointment">
                    @if(!auth()->user()->hasRole('paciente'))
                    <div class="input-block local-forms">
                        <x-input-label for="patient_id" :value="__('patient.title')" required/>
                        <x-select-input  wire:model="patient_id" id="patient_id" name="patient_id" :options="\App\Models\Patient::get()->pluck('name','id')->toArray()"  class="block w-full"/>
                        @error('patient_id') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    @endif
                    @if(!auth()->user()->hasRole('doctor'))
                    <div class="input-block local-forms">
                        <x-input-label for="doctor_id" :value="__('doctor.title')" required/>
                        <x-select-input wire:change="changeDoctor()" wire:model="doctor_id" id="doctor_id" name="doctor_id" :options="\App\Models\Practitioner::get()->pluck('name','id')->toArray()"  class="block w-full"/>
                        @error('doctor_id') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    @endif
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="input-block local-forms">
                            <x-input-label for="Fecha" :value="__('appointment.date')" required/>
                            <input wire:model="appointment_date" type="date" class="form-control-full" required>
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
                    {{--}}
                    <div class="form-group">
                        <label class="form-label">Notas Adicionales</label>
                        <textarea wire:model="notes" class="form-control-full" rows="2" placeholder="Notas adicionales"></textarea>
                        @error('notes') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>
                    @if($editingAppointment)
                    <div class="form-group">
                        <label class="form-label">Estado de la Cita</label>
                        <div class="status-buttons">
                            <button type="button" wire:click="$set('status', 'booked')" class="status-btn status-booked {{ $status === 'booked' ? 'active' : '' }}">Programada</button>
                            <button type="button" wire:click="$set('status', 'confirmed')" class="status-btn status-arrived {{ $status === 'arrived' ? 'active' : '' }}">Llegada</button>
                            <button type="button" wire:click="$set('status', 'in-progress')" class="status-btn status-checked-in {{ $status === 'checked-in' ? 'active' : '' }}">En Progreso</button>
                            <button type="button" wire:click="$set('status', 'fulfilled')" class="status-btn status-fulfilled {{ $status === 'fulfilled' ? 'active' : '' }}">Completada</button>
                            <button type="button" wire:click="$set('status', 'cancelled')" class="status-btn status-cancelled {{ $status === 'cancelled' ? 'active' : '' }}">Cancelada</button>
                            <button type="button" wire:click="$set('status', 'no-show')" class="status-btn status-noshow {{ $status === 'no-show' ? 'active' : '' }}">No Asistió</button>
                        </div>
                    </div>
                    @endif
                    {{--}}
                    <div style="margin-top: 30px; display: flex; gap: 15px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            {{ $editingAppointment ? 'Actualizar Cita' : 'Guardar Cita' }}
                        </button>
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Cancelar</button>
                        @if($editingAppointment)
                            <button type="button" wire:click="deleteAppointment({{ $editingAppointment }})" class="btn" style="background: #dc3545; color: white;" onclick="return confirm('¿Está seguro de eliminar esta cita?')">Eliminar</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    @endif
    <!-- Modal de Configuración de Tiempo -->
    @if($showTimeBlockConfig)
        <div class="modal-overlay" wire:click="toggleTimeBlockConfig">
            <div class="modal-content" wire:click.stop style="max-width: 400px;">
                <div class="modal-header">
                    <h2 class="modal-title">Configuración de Bloques de Tiempo</h2>
                    <button wire:click="toggleTimeBlockConfig" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>

                <form wire:submit="updateTimeBlockConfig">
                    <div class="form-group">
                        <label class="form-label">Tamaño de Bloques</label>
                        <select wire:model="timeBlockMinutes" class="form-control-full">
                            @foreach($timeBlockOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('timeBlockMinutes') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label class="form-label">Hora de Inicio</label>
                            <select wire:model="startHour" class="form-control-full">
                                @for($hour = 0; $hour <= 23; $hour++)
                                    <option value="{{ $hour }}">{{ sprintf('%02d:00', $hour) }}</option>
                                @endfor
                            </select>
                            @error('startHour') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Hora de Fin</label>
                            <select wire:model="endHour" class="form-control-full">
                                @for($hour = 1; $hour <= 24; $hour++)
                                    <option value="{{ $hour }}">{{ sprintf('%02d:00', $hour) }}</option>
                                @endfor
                            </select>
                            @error('endHour') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; font-size: 14px;">
                            <strong>Vista Previa:</strong><br>
                            • Bloques de {{ $timeBlockMinutes }} minutos<br>
                            • Horario: {{ sprintf('%02d:00', $startHour) }} - {{ sprintf('%02d:00', $endHour) }}<br>
                            • Total: {{ (($endHour - $startHour) * 60) / $timeBlockMinutes }} bloques por día
                        </div>
                    </div>

                    <div style="margin-top: 20px; display: flex; gap: 15px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Aplicar Configuración</button>
                        <button type="button" wire:click="toggleTimeBlockConfig" class="btn btn-secondary">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
