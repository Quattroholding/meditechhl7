<div class="card">
    <div class="card-header">
        <h4 class="card-title d-inline-block text-white">{{__('Notas creadas')}}</h4> <a href="{{ url('patients') }}"
            class="patient-views float-end">{{__('Ver todas')}}</a>
    </div>
    <div class="card-body p-0 table-dash">
        <div class="table-responsive">
            <table class="table mb-0 border-0 custom-table">
            @if ($notes->isEmpty())
                    <p class="px-2 py-2">{{__('No ha creado notas')}}</p>
                @else
                <thead>
                    <tr>
                        <th>Paciente</th>
                        <th>Descripción</th>
                        <th>Creación</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($notes as $note)
                    <tr>
                        <td class=" appoint-doctor">
                            <h2>{{$note->patient->name}}</h2>
                        </td>
                        <td class="appoint-time">
                            <h6>{{$note->description}}</h6>
                        </td>
                        <td>
                            <h6>{{$note->created_at->format('l, F j, Y')}}</h6>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
