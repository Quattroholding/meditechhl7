<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white">
            <i class="feather-calendar me-2 text-primary"></i>{{ __('patient.appointments_history') }}
        </h5>
        <span class="badge bg-info">{{ $appointments->total() }} {{ __('patient.insurance.total') }}</span>
    </div>
    <div class="card-body">
        @if($appointments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('patient.appointments.date_time') }}</th>
                            <th>{{ __('patient.appointments.doctor') }}</th>
                            <th>{{ __('patient.status') }}</th>
                            <th>{{ __('patient.appointments.specialty') }}</th>
                            <th>{{ __('patient.appointments.office') }}</th>
                            <th>{{ __('patient.appointments.type') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($appointments as $appointment)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $appointment->start->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $appointment->start->format('H:i') }} - {{ $appointment->end->format('H:i') }}</small>
                            </td>
                            <td>
                                @if($appointment->practitioner)
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm me-2">
                                            @if($appointment->practitioner->avatar())
                                                <img src="{{ url('storage/'.$appointment->practitioner->avatar()->path) }}"
                                                     alt="Avatar" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white"
                                                     style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    {{ substr($appointment->practitioner->given_name, 0, 1) }}{{ substr($appointment->practitioner->family_name, 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ $appointment->practitioner->name }}</div>
                                            <small class="text-muted">{{ $appointment->practitioner->identifier }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">{{ __('patient.appointments.not_assigned') }}</span>
                                @endif
                            </td>
                            <td>
                                <livewire:appointment.status appointment_id="{{$appointment->id}}" wire:key="{{$appointment->id}}"/>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    {{ $appointment->medicalSpeciality?->name ?? __('patient.appointments.general') }}
                                </span>
                            </td>
                            <td>
                                @if($appointment->consultingRoom)
                                    <div class="fw-medium">{{ $appointment->consultingRoom->name }}</div>
                                    <small class="text-muted">{{ $appointment->consultingRoom->location ?? __('patient.appointments.no_location') }}</small>
                                @else
                                    <span class="text-muted">{{ __('patient.appointments.no_office') }}</span>
                                @endif
                            </td>

                            <td>
                                @switch($appointment->appointment_type)
                                    @case('consultation')
                                        <span class="badge bg-light text-dark">{{ __('patient.appointments.consultation') }}</span>
                                        @break
                                    @case('follow-up')
                                        <span class="badge bg-secondary">{{ __('patient.appointments.follow_up') }}</span>
                                        @break
                                    @case('emergency')
                                        <span class="badge bg-danger">{{ __('patient.appointments.emergency') }}</span>
                                        @break
                                    @case('routine')
                                        <span class="badge bg-info">{{ __('patient.appointments.routine') }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">{{ ucfirst($appointment->service_type ?? __('patient.appointments.general')) }}</span>
                                @endswitch
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        {{ __('patient.insurance.showing') }} {{ $appointments->firstItem() }} {{ __('generic.to') }} {{ $appointments->lastItem() }}
                        {{ __('generic.from') }} {{ $appointments->total() }} {{ __('patient.appointments.appointments') }}
                    </small>
                </div>
                @if($appointments->hasMorePages())
                    <button wire:click="loadMore" class="btn btn-outline-primary btn-sm">
                        <i class="feather-plus me-1"></i>{{ __('patient.insurance.load_more') }}
                    </button>
                @endif
            </div>

            <!-- Quick Stats -->
            @php
                $completed = $appointments->where('status', 'completed')->count();
                $cancelled = $appointments->where('status', 'cancelled')->count();
                $noShow = $appointments->where('status', 'no-show')->count();
            @endphp
            @if($completed > 0 || $cancelled > 0 || $noShow > 0)
                <div class="alert alert-light mt-3">
                    <h6><i class="feather-bar-chart me-2"></i>{{ __('patient.appointments.quick_stats') }}</h6>
                    <div class="row text-center">
                        @if($completed > 0)
                            <div class="col-md-4">
                                <div class="text-success">
                                    <strong>{{ $completed }}</strong>
                                    <br><small>{{ __('patient.appointments.completed') }}</small>
                                </div>
                            </div>
                        @endif
                        @if($cancelled > 0)
                            <div class="col-md-4">
                                <div class="text-danger">
                                    <strong>{{ $cancelled }}</strong>
                                    <br><small>{{ __('patient.appointments.cancelled') }}</small>
                                </div>
                            </div>
                        @endif
                        @if($noShow > 0)
                            <div class="col-md-4">
                                <div class="text-warning">
                                    <strong>{{ $noShow }}</strong>
                                    <br><small>{{ __('patient.appointments.no_show') }}</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <div class="empty-state">
                    <i class="feather-calendar text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">{{ __('patient.appointments.no_appointments') }}</h5>
                    <p class="text-muted">{{ __('patient.appointments.no_appointments_message') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
