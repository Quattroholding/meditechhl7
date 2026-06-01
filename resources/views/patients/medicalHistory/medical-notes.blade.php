<div class="medical-notes-content">
    @if($sectionData && count($sectionData) > 0)
     <div class="data-table-container">
            <table class="data-table">
                <thead>
                <tr>
                    <th>{{ __('patient.medical_history.doctor_label') }}</th>
                    <th>{{ __('patient.medical_history.description_label') }}</th>
                    <th>{{ __('patient.medical_history.creation_date') }}</th>
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
                           <h6>{{$note->created_at}}</h6>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @else
    <div style="text-align: center; padding: 60px; color: #64748b;">
        <div style="font-size: 48px; margin-bottom: 20px;">📒</div>
        <h3>{{ __('patient.medical_history.no_notes_registered') }}</h3>
        <p>{{ __('patient.medical_history.no_notes_message') }}</p>
    </div>
    @endif
</div>
