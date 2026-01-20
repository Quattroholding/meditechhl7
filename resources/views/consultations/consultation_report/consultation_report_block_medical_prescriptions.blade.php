<!---------------- MEDICAL PRESCRIPTIONS ------------------------------------->
@if($data->medicationRequests()->count()>0)
@component($table_component,['title'=>trans('consultation.medical_prescriptions')])
    <table class="meds-table" cellspacing="no" border="0" cellpadding="0" style="width:100%">
        <tr class="table-inner-content-head">
            <th style="width:35%">{{__('consultation.name')}}</th>
            <th style="width:65%">Plan</th>
        </tr>
        @foreach($data->medicationRequests()->with(['medicine', 'medication2.ingredients'])->get() as $mp)
            <tr class="table-contents">
                @if($mp->medication2)
                    @php
                        $ingredient = $mp->medication2->ingredients->first();
                        $strength = $ingredient ? $ingredient->strength_value . ' ' . $ingredient->strength_unit : '';
                    @endphp
                    <td>{{ $mp->medication2->display }} {{ $strength }} {{ $mp->medication2->form }}</td>
                    <td>{{ $mp->dosage_text }}</td>
                @elseif($mp->medicine)
                    <td>{{ $mp->medicine->full_name }}</td>
                    <td>{{ $mp->dosage_text }}</td>
                @else
                    <td>{{ $mp->medication }}</td>
                    <td>
                        @if(is_array($mp->dosage_instruction))
                          {{ $mp->dosage_instruction['text'] }}
                        @else
                            {{ $mp->dosage_instruction }}
                        @endif
                    </td>
                @endif
            </tr>
        @endforeach
    </table>
@endcomponent
@endif
