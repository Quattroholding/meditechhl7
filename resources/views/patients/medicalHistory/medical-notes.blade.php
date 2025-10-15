<div class="medical-notes-content">
    @if($sectionData && count($sectionData) > 0)
     <div class="data-table-container">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Doctor</th>
                    <th>Descripción</th>
                    <th>Fecha de Creación</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sectionData as $note)
                    <tr>
                        <td>
                           <h6>{{$note->practitioner->name}}</h6>
                        </td>

                        <td>
                           <h6>{{$note->description}}</h6>
                        </td>
                        <td>
                           <h6>{{$note->created_at->format('d-m-Y H:i')}}</h6>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
    <div style="text-align: center; padding: 60px; color: #64748b;">
        <div style="font-size: 48px; margin-bottom: 20px;">📒</div>
        <h3>No hay Notas Registradas</h3>
        <p>Este paciente no tiene notas médicas registradas en el período seleccionado.</p>
    </div>
    @endif
</div>
