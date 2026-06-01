<div class="medical-calendar-container">
    @section('css')
        <link href="{{url('styles/calendar.css?time='.time())}}" rel="stylesheet" />
    @stop
    <!-- Header -->
    <div class="calendar-header">
        <div class="header-controls">
            <div class="view-buttons">

                <button wire:click="changeView('monthly')" class="btn {{ $currentView === 'monthly' ? 'btn-primary active' : 'btn-secondary' }} btn-fonts">
                    {{ __('appointment.view.monthly') }}
                </button>
                @if(!auth()->user()->hasRole('paciente'))

                <button wire:click="changeView('weekly')" class="btn {{ $currentView === 'weekly' ? 'btn-primary active' : 'btn-secondary' }}">
                    {{ __('appointment.view.weekly') }}
                </button>

                <button wire:click="changeView('daily')" class="btn {{ $currentView === 'daily' ? 'btn-primary active' : 'btn-secondary' }} btn-fonts">
                    {{ __('appointment.view.daily') }}
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
            <div class="view-buttons">
                <button wire:click="openModal" class="btn btn-primary btn-fonts">
                   <i class="fa fa-calendar"></i> {{ __('appointment.new_appointment') }}
                </button>
                @if(!auth()->user()->hasRole('paciente'))
                <button x-on:click="$dispatch('open-modal', 'create_patient')" class="btn btn-secondary btn-fonts">
                    <i class="fa fa-user-injured"></i> {{ __('appointment.register_patient') }}
                </button>
                @endif
            </div>
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
                <input wire:model.live="searchTerm" type="text" placeholder="{{ __('appointment.search_placeholder') }}" class="form-control btn-fonts">
            </div>
            <div>
                @if(!auth()->user()->hasRole('doctor'))
                <select wire:model.live="selectedDoctor" class="form-control btn-fonts">
                    <option value="">{{ __('appointment.all_doctors') }}</option>
                    @foreach($doctors as $key=>$val)
                        <option value="{{ $key }}">{{ $val }}</option>
                    @endforeach
                </select>
                @endif
            </div>
            <div>
                <select wire:model.live="selectedStatus" class="form-control btn-fonts">
                    <option value="">{{ __('appointment.all_statuses') }}</option>
                    <option value="booked">{{ __('appointment.status_booked') }}</option>
                    <option value="arrived">{{ __('appointment.status_arrived') }}</option>
                    <option value="checked-in">{{ __('appointment.status_checked_in') }}</option>
                    <option value="fullfilled">{{ __('appointment.status_fulfilled') }}</option>
                    <option value="cancelled">{{ __('appointment.status_cancelled') }}</option>
                    <option value="noshow">{{ __('appointment.status_noshow') }}</option>
                </select>
            </div>
            <div class="col-xl-3 col-md-6">
                <button wire:click="clearFilters" class="btn btn-secondary w-full btn-fonts">{{ __('appointment.clear_filters') }}</button>
            </div>
            {{--}}
            <div>
                <button wire:click="exportFHIR" class="btn btn-secondary">Exportar FHIR</button>
            </div>

            <div class="col-xl-3 col-md-6 text-end">
                <button wire:click="toggleTimeBlockConfig" class="btn btn-secondary w-full btn-fonts">
                    ⚙️ {{ __('appointment.configure_blocks') }}
                </button>
            </div>
           {{--}}
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
                <div class="legend-color " style="background: linear-gradient(45deg, #{{\App\Enums\AppointmentStatusEnum::Pending->color()}}, #{{\App\Enums\AppointmentStatusEnum::Pending->color()}});"></div>
                <span>{{\App\Enums\AppointmentStatusEnum::Pending->label()}}</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #{{\App\Enums\AppointmentStatusEnum::Booked->color()}}, #{{\App\Enums\AppointmentStatusEnum::Booked->color()}});"></div>
                <span>{{\App\Enums\AppointmentStatusEnum::Booked->label()}}</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #{{\App\Enums\AppointmentStatusEnum::Arrived->color()}}, #{{\App\Enums\AppointmentStatusEnum::Arrived->color()}});"></div>
                <span>{{\App\Enums\AppointmentStatusEnum::Arrived->label()}}</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #{{\App\Enums\AppointmentStatusEnum::CheckedIn->color()}}, #{{\App\Enums\AppointmentStatusEnum::CheckedIn->color()}});"></div>
                <span>{{\App\Enums\AppointmentStatusEnum::CheckedIn->label()}}</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #{{\App\Enums\AppointmentStatusEnum::Fulfilled->color()}}, #{{\App\Enums\AppointmentStatusEnum::Fulfilled->color()}});"></div>
                <span>{{\App\Enums\AppointmentStatusEnum::Fulfilled->label()}}</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #{{\App\Enums\AppointmentStatusEnum::Cancelled->color()}}, #{{\App\Enums\AppointmentStatusEnum::Cancelled->color()}});"></div>
                <span>{{\App\Enums\AppointmentStatusEnum::Cancelled->label()}}</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #{{\App\Enums\AppointmentStatusEnum::NoShow->color()}}, #{{\App\Enums\AppointmentStatusEnum::Cancelled->color()}});"></div>
                <span>{{\App\Enums\AppointmentStatusEnum::NoShow->label()}}</span>
            </div>
            <div class="legend-item">
                <div class="legend-color" style="background: linear-gradient(45deg, #{{\App\Enums\AppointmentStatusEnum::Proposed->color()}}, #{{\App\Enums\AppointmentStatusEnum::Proposed->color()}});"></div>
                <span>{{\App\Enums\AppointmentStatusEnum::Proposed->label()}}</span>
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
                                     :title="$modalTitle"/>

    {{-- Modal de Cancelación --}}
    <x-appointment.cancellation-modal
        :show="$showCancelModal"
        :cancellation-reason="$cancellationReason"
        :custom-cancellation-reason="$customCancellationReason"
    />

    @include('patients.modals.create',['name'=>'create_patient'])

    <!-- Modal de Configuración de Tiempo -->
    @if($showTimeBlockConfig)
        <div class="modal-overlay" wire:click="toggleTimeBlockConfig">
            <div class="modal-content" wire:click.stop style="max-width: 400px;">
                <div class="modal-header">
                    <h2 class="modal-title">{{ __('appointment.time_block_config') }}</h2>
                    <button wire:click="toggleTimeBlockConfig" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>

                <form wire:submit="updateTimeBlockConfig">
                    <div class="form-group">
                        <label class="form-label">{{ __('appointment.block_size') }}</label>
                        <select wire:model="timeBlockMinutes" class="form-control-full">
                            @foreach($timeBlockOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('timeBlockMinutes') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label class="form-label">{{ __('appointment.start_hour') }}</label>
                            <select wire:model="startHour" class="form-control-full">
                                @for($hour = 0; $hour <= 23; $hour++)
                                    <option value="{{ $hour }}">{{ sprintf('%02d:00', $hour) }}</option>
                                @endfor
                            </select>
                            @error('startHour') <span style="color: red; font-size: 12px;">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ __('appointment.end_hour') }}</label>
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
                            <strong>{{ __('appointment.preview') }}:</strong><br>
                            • {{ __('appointment.blocks_of') }} {{ $timeBlockMinutes }} {{ __('appointment.minutes') }}<br>
                            • {{ __('appointment.schedule') }}: {{ sprintf('%02d:00', $startHour) }} - {{ sprintf('%02d:00', $endHour) }}<br>
                            • {{ __('appointment.total') }}: {{ (($endHour - $startHour) * 60) / $timeBlockMinutes }} {{ __('appointment.blocks_per_day') }}
                        </div>
                    </div>

                    <div style="margin-top: 20px; display: flex; gap: 15px;">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">{{ __('appointment.apply_configuration') }}</button>
                        <button type="button" wire:click="toggleTimeBlockConfig" class="btn btn-secondary">{{ __('generic.cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    <script>
        var url = '{{url('/')}}'
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('showToastrAppointment', (event) => {
               console.log(event);
                toastr[event.type](event.message, '', {
                    closeButton: true,
                    progressBar: true,
                    positionClass: 'toast-top-right',
                    timeOut: 5000,
                    onHidden: function() {
                        if(event.reditect_to_encounter){
                            window.location.href = url+'/consultation/'+event.appointment_id; // Replace with your desired URL
                        }
                    }
                });
            });
        });
        </script>
</div>
