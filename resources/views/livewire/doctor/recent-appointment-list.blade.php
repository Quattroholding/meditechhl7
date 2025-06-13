{{-- In work, do what you enjoy. --}}
<div class="col-12 col-lg-12 col-xl-12 d-flex">
    <style>
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .btn-confirm { background: #3498db; color: white; }
        .btn-start { background: #f39c12; color: white; }
        .btn-complete { background: #27ae60; color: white; }
        .btn-cancel { background: #e8536e; color: white; }
        .btn-edit { background: #9b59b6; color: white; }
    </style>
    <div class="card flex-fill comman-shadow">
        <div class="card-header">
            <h4 class="card-title d-inline-block" style="color: white">{{__('Citas recientes')}}</h4> <a
                href="{{route('appointment.index')}}" class="patient-views float-end">{{__('Ver todas')}}</a>
        </div>
        <div class="card-body">
            @if ($appointments->isEmpty())
            <p class="px-2">{{__('Sin citas programadas para hoy')}}</p>
            @else
            <div class="teaching-card">
                <ul class="steps-history">
                    @foreach ($appointments->groupBy('start') as $time => $group)
                    <li>{{ \Carbon\Carbon::parse($time)->format('h:i A') }}</li>
                    @endforeach
                </ul>
                <ul class="activity-feed">
                    @foreach ($appointments->groupBy('start') as $time => $group)
                    <li class="feed-item d-flex align-items-center">
                        <div class="dolor-activity hide-activity">
                            <ul class="doctor-date-list mb-2">
                                @foreach ($group as $appointment)
                                @php
                                $appointmentTime = Carbon\Carbon::parse($appointment->start);
                                $isPast = $appointmentTime->isPast();
                                @endphp
                                <li
                                    class="{{ in_array($appointment->status, ['booked', 'arrived']) ? 'dropdown ongoing-blk' : ($isPast ? 'past-appointment' : 'stick-line') }}">
                                    <i
                                        class="fas fa-circle me-2 {{ $appointment->status == 'fulfilled' ? 'active-circles' : '' }}"></i>{{
                                    \Carbon\Carbon::parse($time)->format('h:i') }}
                                    <span
                                        title="{{ !in_array($appointment->status, ['booked', 'arrived', 'fulfilled']) ? 'this appointment has a status of ' . $appointment->status : '' }}">{{
                                        $appointment->patient->name }}</span>
                                </li>
                                @if (in_array($appointment->status, ['booked', 'arrived']))
                                <a href="#" class="dropdown-toggle  active-doctor" data-bs-toggle="dropdown">
                                    <i class="fas fa-circle me-2 active-circles"></i>{{--}}{{
                                    \Carbon\Carbon::parse($time)->format('h:i') }}{{--}}
                                    <span class='mx-2'>{{ $appointment->patient->name }}</span><span
                                        class="ongoing-drapt"> <button type="button"
                                            class="badge appointment-status-{{$appointment->status}}">
                                            {{ __('appointment.status.'.$appointment->status) }}
                                        </button><i class="fa fa-chevron-down ms-2"></i></span>
                                </a>
                                <ul class="doctor-sub-list dropdown-menu">
                                    <li class="patient-new-list dropdown-item">
                                        {{__('patient.title')}}<span>{{ $appointment->patient->name }}</span><a
                                            href="javascript:;" class="new-dot status-green"><i
                                                class="fas fa-circle me-1 fa-2xs"></i>{{__('generic.new')}}</a></li>
                                    <li class="dropdown-item">{{__('appointment.reason')}}<span>{{
                                            $appointment->service_type }}</span><a href="javascript:;"></li>
                                    <li class="dropdown-item">
                                        {{__('Hora')}}<span>{{ \Carbon\Carbon::parse($appointment->start)->format('h:i')
                                            }}
                                            -
                                            {{ \Carbon\Carbon::parse($appointment->end)->format('h:i A') }}
                                            ({{ $appointment->minutes_duration }} min)</span></li>
                                    <li class="schedule-blk mb-0 pt-2 dropdown-item">
                                        <ul class="nav schedule-time">
                                            <li><a href=""><img src="../assets/img/icons/trash.svg" alt=""></a></li>
                                            <li><a href="{{route('patient.profile', $appointment->patient->id)}}"><img
                                                        src="../assets/img/icons/profile.svg" alt=""></a></li>
                                            <li><a wire:click="editAppointment({{$appointment->id}})"><img
                                                        src="../assets/img/icons/edit.svg" alt=""></a></li>
                                        </ul>
                                        @if(auth()->user()->can('arrived',$appointment))
                                            <button wire:click.stop="updateStatus({{ $appointment->id }}, 'arrived')" class="action-btn btn-start">
                                                🚪 Registrar Llegada
                                            </button>
                                        @endif
                                        @if(auth()->user()->can('checked_in',$appointment))
                                            <button wire:click.stop="updateStatus({{ $appointment->id }}, 'checked-in')" class="action-btn btn-start">
                                                ▶️ Iniciar
                                            </button>
                                        @endif
                                        @if(auth()->user()->can('viewConsultation',$appointment))
                                            <a href="{{route('consultation.show',$appointment->id)}}" class="action-btn btn-start">
                                                ▶️ Ver Consulta
                                            </a>
                                        @endif
                                        @if(auth()->user()->can('cancelled',$appointment))
                                            <button wire:click.stop="updateStatus({{ $appointment->id }}, 'canceled')"  class="action-btn btn-cancel">
                                                ❌ Cancelar
                                            </button>
                                        @endif

                                        @if(auth()->user()->can('noshow',$appointment))
                                            <button wire:click.stop="updateStatus({{ $appointment->id }}, 'noshow')"  class="action-btn btn-cancel">
                                                👻 No aparecio
                                            </button>
                                        @endif
                                    </li>
                                </ul>
                                @endif
                    </li>
                    <script>
                        document.addEventListener('livewire:initialized', () => {
                            Livewire.on('showToastr{{$appointment->id}}', (event) => {
                                toastr[event.type](event.message, '', {
                                    closeButton: true,
                                    progressBar: true,
                                    positionClass: 'toast-top-right',
                                    timeOut: 5000,
                                    onHidden: function() {
                                        window.location.href = '{{route('consultation.show',$appointment->id)}}'; // Replace with your desired URL
                                    }
                                });
                            });
                        });
                    </script>
                    @endforeach
                </ul>
            </div>
            </li>
            @endforeach
            </ul>
            @endif
        </div>
        <livewire:appointment.modal-save wire:model="showModal" :title="$modalTitle"
            :appointment_date="$appointment_date" :appointment_time="$appointment_time" />
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function() {
            setInterval(function() {
                Livewire.emit('refreshAppointments');
            }, 60000);
            console.log('actualizó'); // Actualizar cada minuto
        });
</script>
