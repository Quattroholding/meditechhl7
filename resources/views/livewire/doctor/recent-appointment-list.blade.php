{{-- In work, do what you enjoy. --}}
<div class="">
    <style>
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 5px;
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

        /* Loading skeleton styles */
        .loading-skeleton {
            animation: pulse 1.5s ease-in-out infinite;
        }

        .skeleton-appointment-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .skeleton-time {
            width: 60px;
            height: 20px;
            background: #e9ecef;
            border-radius: 4px;
        }

        .skeleton-patient-info {
            flex: 1;
        }

        .skeleton-name {
            height: 18px;
            background: #e9ecef;
            border-radius: 4px;
            margin-bottom: 8px;
            width: 70%;
        }

        .skeleton-status {
            height: 14px;
            background: #e9ecef;
            border-radius: 4px;
            width: 40%;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>

    <div  class="card flex-fill comman-shadow" >

        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">
                <i class="fas fa-calendar-alt me-2"></i>
                {{ __('doctor.dashboard.todays_appointments') }}
            </h4>
            <div class="dropdown">
                <button wire:click="openModal" class="btn btn-success float-end">+ {{ __('doctor.dashboard.new_appointment') }}</button>
            </div>
        </div>

        <div class="card-body">
            @if($isLoading)

                <div class="loading-skeleton">
                    <div class="skeleton-list">
                        @for($i = 0; $i < 5; $i++)
                            <div class="skeleton-appointment-item mb-3">
                                <div class="skeleton-time"></div>
                                <div class="skeleton-patient-info">
                                    <div class="skeleton-name"></div>
                                    <div class="skeleton-status"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            @elseif ($appointments->isEmpty())
            <p class="px-2">{{ __('doctor.dashboard.no_appointments_today') }}</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
            @else
            <ul class="teaching-card">
                <ul class="steps-history">
                    @foreach ($appointments->groupBy('start') as $time => $group)
                    <li>{{ \Carbon\Carbon::parse($time)->format('h:i A') }}</li>
                    @endforeach
                </ul>
                <div class="activity-feed">
                    @foreach ($appointments->groupBy('start') as $time => $group)
                    <li class="feed-item d-flex align-items-center">
                        <div class="dolor-activity hide-activity">
                            <ul class="doctor-date-list mb-2"  id="miDiv">
                                @foreach ($group as $appointment)
                                    @php
                                    $appointmentTime = Carbon\Carbon::parse($appointment->start);
                                    $isPast = $appointmentTime->isPast();
                                    $ongoing = now()->isBetween(\Carbon\Carbon::parse($appointment->start),\Carbon\Carbon::parse($appointment->end));
                                    $status = $appointment->status->value;
                                    @endphp
                                    @if(!$ongoing)

                                    <li class="{{ in_array($appointment->status->value, ['booked','confirm', 'arrived']) ? 'dropdown ' : ($isPast ? 'past-appointment' : 'stick-line') }}">
                                        {{--}}
                                        <i class="fas fa-circle me-2 {{ $appointment->status->value == 'fulfilled' ? 'active-circles' : '' }}"></i>
                                        {{ \Carbon\Carbon::parse($time)->format('h:i') }}
                                        {{--}}
                                        <a href="" title="{{ !in_array($appointment->status->value, ['booked','confirm', 'arrived', 'fulfilled']) ? 'this appointment has a status of ' .$status  : '' }}"
                                           @can('patients.medical_history') data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight{{$appointment->patient_id}}" aria-controls="offcanvasRight" @endcan>
                                            {{ $appointment->patient->name }}
                                        </a>
                                        @if(auth()->user()->hasRole('recepcionista'))
                                            ({{$appointment->practitioner->name}})
                                        @endif
                                        <div class="float-end">
                                            {{--}}
                                            <span class="badge appointment-status-{{$status}}" style="color:#fff;">  {{ __('appointment.status.'.$status) }}</span>
                                            {{--}}

                                            <livewire:appointment.status :appointment_id="$appointment->id" wire:key="{{$appointment->id}}"/>

                                        </div>
                                    </li>
                                    @endif
                                    @if (in_array($appointment->status->value, ['booked','confirm', 'arrived','checked-in']))
                                        @if($ongoing)
                                        <a id="destino" class="dropdown-toggle active-doctor"  data-bs-toggle="dropdown">
                                            <i class="fas fa-circle me-2 active-circles"></i>
                                            <span class='mx-2' >{{ $appointment->patient->name }}</span>

                                            <span   class="ongoing-drapt">
                                              {{ __('doctor.dashboard.ongoing') }}
                                                <i class="fa fa-chevron-down ms-2"></i>
                                            </span>
                                        </a>
                                        @endif
                                        <ul class="doctor-sub-list dropdown-menu">
                                            <li class="patient-new-list dropdown-item">
                                                {{__('patient.title')}} : <button >{{ $appointment->patient->name }}</button>
                                                <a href="javascript:;" class="new-dot {{$appointment->status->badgeClass()}}">
                                                    {{$appointment->status->label()}}
                                                </a>
                                            </li>
                                            <li class="dropdown-item">{{__('appointment.reason')}}
                                                <span>{{  $appointment->service_type }}   </span>
                                                <a href="javascript:;"></a>
                                            </li>
                                            <li class="dropdown-item">
                                                {{ __('doctor.dashboard.time') }}
                                                <span>
                                                    {{ \Carbon\Carbon::parse($appointment->start)->format('h:i')  }} -
                                                    {{ \Carbon\Carbon::parse($appointment->end)->format('h:i A') }}({{ $appointment->minutes_duration }} min)
                                                </span>
                                            </li>
                                            <li class="schedule-blk mb-0 pt-2 dropdown-item">
                                                <ul class="nav schedule-time">
                                                    @can('patients.medical_history')
                                                    <li><a href="{{route('patient.medical_history', $appointment->patient->id)}}" >
                                                            <img src="../assets/img/icons/profile.svg" alt="" style="cursor: pointer;"></a>
                                                    </li>
                                                    @endcan
                                                    @can('appointments.edit')
                                                    <li>
                                                        <a wire:click="editAppointment({{$appointment->id}})" style="cursor: pointer;"><img src="../assets/img/icons/edit.svg" alt=""></a>
                                                    </li>
                                                    @endcan
                                                </ul>
                                                @if(auth()->user()->can('arrived',$appointment))
                                                    <button wire:click.stop="updateStatus({{ $appointment->id }}, 'arrived')" class="action-btn btn-start">
                                                        🚪 {{ __('doctor.dashboard.register_arrival') }}
                                                    </button>
                                                @endif
                                                @if(auth()->user()->can('checked_in',$appointment))
                                                    <button wire:click.stop="updateStatus({{ $appointment->id }}, 'checked-in')" class="action-btn btn-start">
                                                        ▶️ {{ __('doctor.dashboard.start_consultation') }}
                                                    </button>
                                                @endif
                                                @if(auth()->user()->can('viewConsultation',$appointment))
                                                    <a href="{{route('consultation.show',$appointment->id)}}" class="action-btn btn-confirm">
                                                         @if($appointment->encounter) {{ __('doctor.dashboard.edit_consultation') }} @else ✏️ {{ __('doctor.dashboard.fill_consultation') }}@endif

                                                    </a>
                                                @endif
                                                @if(auth()->user()->can('cancelled',$appointment))
                                                    <button wire:click.stop="updateStatus({{ $appointment->id }}, 'canceled')"  class="action-btn btn-cancel">
                                                        ❌ {{ __('doctor.dashboard.cancel') }}
                                                    </button>
                                                @endif

                                                @if(auth()->user()->can('noshow',$appointment))
                                                    <button wire:click.stop="updateStatus({{ $appointment->id }}, 'noshow')"  class="action-btn btn-cancel">
                                                        👻 {{ __('doctor.dashboard.no_show') }}
                                                    </button>
                                                @endif
                                            </li>
                                        </ul>
                                    @endif
                                    @include('consultations.partials.patient_info',array('id'=>$appointment->patient_id))
                                    <script>
                                        document.addEventListener('livewire:initialized', () => {
                                            alert('recent-appointment-list');
                                            Livewire.on('showToastr{{$appointment->id}}', (event) => {
                                                toastr[event[0].type](event[0].message, '', {
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
                </div>
            </ul>
            @endif
        </div>
    </div>

<livewire:appointment.modal-save wire:model="showModal" :title="$modalTitle"
                                 :appointment_date="$appointment_date" :appointment_time="$appointment_time" />

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const container = document.getElementById("miDiv");
            const destino = document.getElementById("destino");


            if (container && destino) {
                container.scrollTop = destino.offsetTop;
                console.log(destino);

                destino.style.transition = 'background-color 0.5s ease';
                destino.style.backgroundColor = '#ffff99';
                setTimeout(() => destino.style.backgroundColor = '', 2000);
            }

            setInterval(function() {
                Livewire.dispatch('loadAppointments');
                console.log('actualizó'); // Actualizar cada minuto
            }, 30000);
        });
    </script>

    {{-- Modal de Asignación Manual de Slot
    <livewire:appointment.manual-slot-assignment />
    --}}

</div>


