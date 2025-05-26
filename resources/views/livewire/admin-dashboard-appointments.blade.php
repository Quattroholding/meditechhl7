<div class="col-12 col-md-12  col-xl-8">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title d-inline-block" style="color: #fff;">Upcoming Appointments</h4> <a
                href="{{ url('appointments') }}" class="patient-views float-end">Show all</a>
        </div>
        <div class="card-body p-0 table-dash">
            @if($appointments->isEmpty())
                <p class="px-4 pt-3">No appointment found.</p>
            @else
            <div class="table-responsive">
                <table class="table mb-0 border-0 datatable custom-table">
                    <thead>
                        <tr>
                            <th>
                                <div class="form-check check-tables">
                                    <input class="form-check-input" type="checkbox" value="something">
                                </div>
                            </th>
                            <th>No</th>
                            <th>Patient name</th>
                            <th>Doctor</th>
                            <th>Time</th>
                            <th>Reason for Visit</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $json = file_get_contents(public_path('../public/assets/json/admin-dashboard-appointments.json'));
                            $dashboards = json_decode($json, true);
                        @endphp
                        @foreach($appointments as $appointment)
                        <tr>
                            <td>
                                <div class="form-check check-tables">
                                    <input class="form-check-input" type="checkbox" value="something">
                                </div>
                            </td>
                            <td>
                                {{$appointment->id}}
                            </td>
                            <td>
                                {{$appointment->patient->name}}
                            </td>
                            <td class="table-image appoint-doctor">
                                <img width="28" height="28" class="rounded-circle"
                                        src="{{ URL::asset('/assets/img/profiles/' . $appointment->patient->user->profile_picture) }}"
                                        alt="">
                                <h2>{{$appointment->practitioner->name}}</h2>
                            </td>
                            @php 
                            $date = \Carbon\Carbon::parse($appointment->created_at);
                            $format_date = $date->format('M d Y H:i A');
                            @endphp
                            <td class="appoint-time">
                                <span>{{ $date->format('M d Y') }} at
                                    </span>{{ $date->format('H:i A') }}
                            </td>
                            <td><button class="custom-badge status-green">{{$appointment->service_type}}</button></td>
                                                            <td class="text-end">
                                    <div class="dropdown dropdown-action">
                                        <a href="javascript:;" class="action-icon dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                                class="fa fa-ellipsis-v"></i></a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item"
                                                href="{{ url('edit-appointment') }}"><i
                                                    class="fa-solid fa-pen-to-square m-r-5"></i> Edit</a>
                                            <a class="dropdown-item" href="javascript:;"
                                                data-bs-toggle="modal"
                                                data-bs-target="#delete_patient"><i
                                                    class="fa fa-trash-alt m-r-5"></i> Delete</a>
                                        </div>
                                    </div>
                                </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>