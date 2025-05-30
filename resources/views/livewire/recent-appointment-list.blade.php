    {{-- In work, do what you enjoy. --}}

    <div class="col-12 col-lg-12 col-xl-5 d-flex">
        <div class="card flex-fill comman-shadow">
            <div class="card-header">
                <h4 class="card-title d-inline-block" style="color: white">Recent Appointments</h4> <a
                    href="{{route('appointment.index')}}" class="patient-views float-end">Show all</a>
            </div>
            <div class="card-body">
                @if ($appointments->isEmpty())
                    <p class="px-2">No appointments found for today.</p>
                @else
                    <div class="teaching-card">
                        <ul class="steps-history">
                            @foreach ($appointments->groupBy('start') as $time => $group)
                                <li>{{ \Carbon\Carbon::parse($time)->format('h:i') }}</li>
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
                                                    class="{{ in_array($appointment->status, ['booked', 'arrived', 'fulfilled']) ? 'dropdown ongoing-blk' : ($isPast ? 'past-appointment' : 'stick-line') }}">
                                                    <i
                                                        class="fas fa-circle me-2 {{ $appointment->status == 'fulfilled' ? 'active-circles' : '' }}"></i>{{ \Carbon\Carbon::parse($time)->format('h:i') }}
                                                    <span
                                                        title="{{ !in_array($appointment->status, ['booked', 'arrived', 'fulfilled']) ? 'this appointment has a status of ' . $appointment->status : '' }}">{{ $appointment->patient->name }}</span>
                                                </li>
                                                @if (in_array($appointment->status, ['booked', 'arrived', 'fulfilled']))
                                                    <a href="#" class="dropdown-toggle  active-doctor"
                                                        data-bs-toggle="dropdown">
                                                        <i
                                                            class="fas fa-circle me-2 active-circles"></i>{{ \Carbon\Carbon::parse($time)->format('h:i') }}
                                                        <span class='mx-2'>{{ $appointment->patient->name }}</span><span
                                                            class="ongoing-drapt">Checked in<i
                                                                class="feather-chevron-down ms-2"></i></span>
                                                    </a>
                                                    <ul class="doctor-sub-list dropdown-menu">
                                                        <li class="patient-new-list dropdown-item">
                                                            Patient<span>{{ $appointment->patient->name }}</span><a
                                                                href="javascript:;" class="new-dot status-green"><i
                                                                    class="fas fa-circle me-1 fa-2xs"></i>New</a></li>
                                                        <li class="dropdown-item">Reason for
                                                            Visit<span>{{ $appointment->service_type }}</span><a
                                                                href="javascript:;"></li>
                                                        <li class="dropdown-item">
                                                            Time<span>{{ \Carbon\Carbon::parse($appointment->start)->format('h:i') }}
                                                                -
                                                                {{ \Carbon\Carbon::parse($appointment->end)->format('h:i A') }}
                                                                ({{ $appointment->minutes_duration }} min)</span></li>
                                                        <li class="schedule-blk mb-0 pt-2 dropdown-item">
                                                            <ul class="nav schedule-time">
                                                                <li><a href="javascript:;"><img
                                                                            src="assets/img/icons/trash.svg"
                                                                            alt=""></a></li>
                                                                <li><a href="javascript:;"><img
                                                                            src="assets/img/icons/profile.svg"
                                                                            alt=""></a></li>
                                                                <li><a href="javascript:;"><img
                                                                            src="assets/img/icons/edit.svg"
                                                                            alt=""></a></li>
                                                            </ul>
                                                            <a class="btn btn-primary appoint-start">Start
                                                                Appointment</a>
                                                        </li>
                                                    </ul>
                                                @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    </li>
                @endforeach
                </ul>
                @endif
            </div>
        </div>
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
