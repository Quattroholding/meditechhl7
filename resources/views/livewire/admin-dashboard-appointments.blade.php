<div class="col-12 col-md-12  col-xl-12">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title d-inline-block" style="color: #fff;">{{__('Proximas citas')}}</h4> <a
                href="{{ route('appointment.index') }}" class="patient-views float-end text-white">{{__('Ver todas')}}</a>
        </div>
        <div class="card-body p-0 table-dash">
            @if($data->isEmpty())
                <p class="px-4 pt-3">No appointment found.</p>
            @else
            <div class="table-responsive">
                <table class="table mb-0 border-0 custom-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{__('patient.title')}}</th>
                            <th>{{__('doctor.title')}}</th>
                            <th>{{__('Dia')}}</th>
                            <th>{{__('Hora')}}</th>
                            <th>{{__('Razon de visita')}}</th>
                            {{--}}
                            <th>{{__('Acciones')}}</th>
                            {{--}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $appointment)
                        <tr>
                            <td>
                                {{$appointment->id}}
                            </td>
                            <td>
                                {!!  $appointment->patient->profile_name!!}
                            </td>
                            <td class="table-image appoint-doctor">
                                <h2>{!!  $appointment->practitioner->profile_name!!}</h2>
                            </td>
                            <td class="appoint-time">{{$appointment->start->format('M d Y')}}</td>
                            <td class="appoint-time">{{$appointment->start->format('H:i A')}}</td>
                            </td>
                            <td>
                                <button class="custom-badge status-green">{{$appointment->service_type}}</button>
                            </td>
                            {{--}}
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
                                {{--}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-3" class="float-right">
                    {{ $data->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
