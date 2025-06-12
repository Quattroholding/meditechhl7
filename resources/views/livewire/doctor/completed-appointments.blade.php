<div class="card">
    <div class="card-header">
        <h4 class="card-title d-inline-block text-white">Completed Appointments</h4> <a href="{{ url('appointments') }}"
            class="patient-views float-end">Show all</a>
    </div>
    <div class="card-body p-0 table-dash">
        <div class="table-responsive">
            <table class="table mb-0 border-0 custom-table">
                <tbody>
                @if ($completedAppointments->isEmpty())
                    <p class="px-2 py-2">No appointments completed today.</p>
                @else
                    @foreach ($completedAppointments as $appointment)
                    <tr>
                        <td class="table-image appoint-doctor">
                            <img width="28" height="28" class="rounded-circle"
                                src="{{ URL::asset('/assets/img/profiles/avatar-02.jpg') }}" alt="">
                            <h2>{{$appointment->patient->name}}</h2>
                        </td>
                        <td class="appoint-time text-center">
                            <h6>{{$appointment->start->format('l, F j, Y')}}</h6>
                            <span>{{$appointment->service_type}}</span>
                        </td>
                        <td>
                            <button class="check-point status-green me-1"><i class="feather-check"></i></button>
                            <button class="check-point status-pink "><i class="feather-x"></i></button>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>