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
                @if(!auth()->user()->hasRole('paciente'))
                    {{--}}
                <button wire:click="changeView('weekly')" class="btn {{ $currentView === 'weekly' ? 'btn-primary active' : 'btn-secondary' }}">
                    Semanal
                </button>
                {{--}}
                <button wire:click="changeView('daily')" class="btn {{ $currentView === 'daily' ? 'btn-primary active' : 'btn-secondary' }}">
                    Diaria
                </button>
                @endif
            </div>

            <div class="navigation">
                <button wire:click="navigateCalendar(-1)" class="nav-btn">‹</button>
                <div class="current-period">{{ $currentPeriod }}</div>
                <button wire:click="navigateCalendar(1)" class="nav-btn">›</button>
                {{--}}
                <button wire:click="goToToday" class="btn btn-secondary" style="margin-left: 10px;">Hoy</button>
                {{--}}
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
                @foreach($doctors as $key=>$val)
                    <option value="{{ $key }}">{{ $val }}</option>
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
        <div class="col-xl-3 col-md-6">
            <button wire:click="clearFilters" class="btn btn-secondary w-full">Limpiar Filtros</button>
        </div>
        {{--}}
        <div>
            <button wire:click="exportFHIR" class="btn btn-secondary">Exportar FHIR</button>
        </div>
        {{--}}
        <div class="col-xl-3 col-md-6 text-end">
            <button wire:click="toggleTimeBlockConfig" class="btn btn-secondary w-full">
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
    <livewire:appointment.modal-save wire:model="showModal"
                                     :title="$modalTitle"
                                     :appointment_date="$appointment_date"
                                     :appointment_time="$appointment_time"/>

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
