<!---------------- REPORTE ------------------------------------->




@component($table_component,['title'=>'International Prostate Symptom Index'])

<?php $label = ""; ?>
    <div class="score-prostate-score-section-label">
        Score:
        <span
                @if($Test_Urologia_Resultado < 8) class="score_prostate_mild" @php $label ="Mild" @endphp @endif
                @if($Test_Urologia_Resultado >= 8 && $Test_Urologia_Resultado < 20) class="score_prostate_moderate" @php $label ="Moderate" @endphp @endif
                @if($Test_Urologia_Resultado >= 20) class="score_prostate_severe" @php $label ="Severe" @endphp @endif
        >
        {{ $Test_Urologia_Resultado }} - <span >{{ $label }}</span>
        </span>
    </div>



    <table class="result-table" cellspacing="no" cellpadding="0">
        <tr class="table-inner-content-head">
            <td>QUESTION</td>
            <td>ANSWER</td>
        </tr>

    </table>




    @include('consultation.helpers.inputs.labeled-score-display',['oddeven'=>0,'question'=>$consultation_speciality_question[1],'appointment_id'=>$appointment_id, 'consultation_id'=>$consultation_id,'consultation_speciality_question_answer'=>$consultation_speciality_question_answer])

    @include('consultation.helpers.inputs.labeled-score-display',['oddeven'=>1,'question'=>$consultation_speciality_question[2],'appointment_id'=>$appointment_id, 'consultation_id'=>$consultation_id,'consultation_speciality_question_answer'=>$consultation_speciality_question_answer])

    @include('consultation.helpers.inputs.labeled-score-display',['oddeven'=>0,'question'=>$consultation_speciality_question[3],'appointment_id'=>$appointment_id, 'consultation_id'=>$consultation_id,'consultation_speciality_question_answer'=>$consultation_speciality_question_answer])

    @include('consultation.helpers.inputs.labeled-score-display',['oddeven'=>1,'question'=>$consultation_speciality_question[4],'appointment_id'=>$appointment_id, 'consultation_id'=>$consultation_id,'consultation_speciality_question_answer'=>$consultation_speciality_question_answer])

    @include('consultation.helpers.inputs.labeled-score-display',['oddeven'=>0,'question'=>$consultation_speciality_question[5],'appointment_id'=>$appointment_id, 'consultation_id'=>$consultation_id,'consultation_speciality_question_answer'=>$consultation_speciality_question_answer])

    @include('consultation.helpers.inputs.labeled-score-display',['oddeven'=>1,'question'=>$consultation_speciality_question[6],'appointment_id'=>$appointment_id, 'consultation_id'=>$consultation_id,'consultation_speciality_question_answer'=>$consultation_speciality_question_answer])

    @include('consultation.helpers.inputs.labeled-score-display',['oddeven'=>0,'question'=>$consultation_speciality_question[7],'appointment_id'=>$appointment_id, 'consultation_id'=>$consultation_id,'consultation_speciality_question_answer'=>$consultation_speciality_question_answer])

    <hr>

    @include('consultation.helpers.inputs.labeled-score-display',['oddeven'=>1,'question'=>$consultation_speciality_question[8],'appointment_id'=>$appointment_id, 'consultation_id'=>$consultation_id,'consultation_speciality_question_answer'=>$consultation_speciality_question_answer])


    <style>

        .score-prostate-score-section-label{
            font-size:20px;
            padding:10px;
            font-weight:bold;
        }

        .score_prostate_mild {
            color: #0cc900;
        }

        .score_prostate_moderate {
            color: #cec200;
        }

        .score_prostate_severe {
            color: #f10202;
        }


    </style>




@endcomponent

