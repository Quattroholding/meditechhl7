<div class="row">
    <div class="col-12 col-md-12  col-xl-7">
       <livewire:activity-chart />
        <div class="row">
            <div class="col-12 col-md-12  col-xl-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title d-inline-block">Recent Appointments</h4> <a
                            href="{{ url('appointments') }}" class="patient-views float-end">Show all</a>
                    </div>
                    <div class="card-body p-0 table-dash">
                        <div class="table-responsive">
                            <table class="table mb-0 border-0 custom-table">
                                <tbody>
                                <tr>
                                    <td class="table-image appoint-doctor">
                                        <img width="28" height="28" class="rounded-circle"
                                             src="{{ URL::asset('/assets/img/profiles/avatar-02.jpg') }}"
                                             alt="">
                                        <h2>Dr.Jenny Smith</h2>
                                    </td>
                                    <td class="appoint-time text-center">
                                        <h6>Today 5:40 PM</h6>
                                        <span>Typoid Fever</span>
                                    </td>
                                    <td>
                                        <button class="check-point status-green me-1"><i
                                                class="feather-check"></i></button>
                                        <button class="check-point status-pink "><i class="feather-x"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-image appoint-doctor">
                                        <img width="28" height="28" class="rounded-circle"
                                             src="{{ URL::asset('/assets/img/profiles/avatar-03.jpg') }}"
                                             alt="">
                                        <h2>Dr.Angelica Ramos</h2>
                                    </td>
                                    <td class="appoint-time text-center">
                                        <h6>Today 5:40 PM</h6>
                                        <span>Typoid Fever</span>
                                    </td>
                                    <td>
                                        <button class="check-point status-green me-1"><i
                                                class="feather-check"></i></button>
                                        <button class="check-point status-pink "><i class="feather-x"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="table-image appoint-doctor">
                                        <img width="28" height="28" class="rounded-circle"
                                             src="{{ URL::asset('/assets/img/profiles/avatar-04.jpg') }}"
                                             alt="">
                                        <h2>Dr.Martin Doe</h2>
                                    </td>
                                    <td class="appoint-time text-center">
                                        <h6>Today 5:40 PM</h6>
                                        <span>Typoid Fever</span>
                                    </td>
                                    <td>
                                        <button class="check-point status-green me-1"><i
                                                class="feather-check"></i></button>
                                        <button class="check-point status-pink "><i class="feather-x"></i></button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @livewire('next-appointment')
        </div>
    </div>
    @livewire('recent-appointment-list')
</div>
