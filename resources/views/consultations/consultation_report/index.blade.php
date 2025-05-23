<html>
<head>
    <?php

    $table_component = 'consultations.consultation_report.consultation_report_table';
    $consultation_list = [];
    foreach ($data['consultation-list'] as $item) {

        if($lang=='esp')
            $consultation_list[$item->id] = $item->esp_value;
        else
            $consultation_list[$item->id] = $item->value;

    }
    $color_texto = '#3c3c3c';
    ?>
    @include("consultations.consultation_report.consultation_report_styles")

</head>
<body style="max-width: 800px;margin: 0 auto;">
<header>
    <!-- Logo según pais -->
    <!-- Panamá = 1 -->
    @if(request()->has('html'))
        <img style="max-height:56px" src="{{ url('assets/img/logo.png') }}">
    @else
        <img style="max-height:56px" src="{{ public_path('assets/img/logo.png') }}">
    @endif
    {{--}}
    @if($data['appointment']->clinic()->first()->pais != "1")
        <img style="width:110px" src="{{ public_path('/images/SCB.png') }}">
    @else
        <img style="width:30%" src="{{ public_path('/images/doctor-care-logo.jpg') }}">
    @endif
    {{--}}

    <table style="width:100%;max-width: 800px;margin: 0 auto;">
        <tr>
            <td style="width:50%">
                <div class="subtitle-header upper">{{ __('consultation.consultation') }}
                    <b style="color: {{$color_texto}};font-weight: bold;">#{{ $data->identifier }}</b>
                    @if($home_visit)
                        <span class="upper"><b>{{ __('consultation.home_visit') }}</b></span>
                    @endif
                    <br>
                    <br>

                    <span class="upper">{{__('consultation.date')}}</span>
                    :
                    <b style="color: {{$color_texto}};">

                        <?php

                        $fecha = Carbon\Carbon::parse($data->appointment->end);
                        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $daysOfWeekEs = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

                        $monthsOfYear = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                        $monthsOfYearEs = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

                        if (App::getLocale() == 'en') {
                            echo "<b style=\"color: {$color_texto};\">".strtoupper($fecha->format('l jS \of F Y h:i:s A'))."</b>";
                        } elseif (App::getLocale() == 'es') {
                            $formattedDate = $fecha->format('l j \of F Y h:i:s A');
                            $formattedDate = str_replace($daysOfWeek, $daysOfWeekEs, $formattedDate);
                            $formattedDate = str_replace($monthsOfYear, $monthsOfYearEs, $formattedDate);
                            $formattedDate = str_replace('of', 'de', $formattedDate);
                            echo "<b style=\"color: {$color_texto};\">".strtoupper($formattedDate)."</b>";
                        }

                        ?>


                    </b>


                </div>
            </td>
            <td style="width:50%;vertical-align: top">
                <div class="white-box right subtitle-header upper">{{__('consultation.patient')}}:
                    <b style="color:{{$color_texto}};font-size:17px;">{{ $data->patient->name }} (#{{ $data->patient_id }})</b>
                </div>
                <div class="gray-box right subtitle-header upper">{{__('consultation.dob')}}:
                    <b style="color:{{$color_texto}};">{{ strtoupper(Carbon\Carbon::parse($data->patient->birth_date)->format('F-d-Y')) }} |</b>
                    <?php

                    $fdate = $data->end;
                    $tdate = $data->patient->birth_date;
                    $datetime1 = new DateTime($fdate);
                    $datetime2 = new DateTime($tdate);
                    $interval = $datetime1->diff($datetime2);
                    $age = $interval->format('%y');

                    ?>
                    <span class="upper">{{__('consultation.age')}}</span>
                    :
                    <b style="color:{{$color_texto}};">{{ $age }} |</b>
                    <span class="upper">{{__('consultation.gender')}}</span>
                    :
                    <b style="color:{{$color_texto}};">{{ $data->patient->gender }}</b>
                </div>
            </td>
        </tr>

        <tr>
            <td colspan="2" valign="top">
                <div style=" padding: 2px 0px;width:100%">
                    <div style="width:100%;height: 3px;background-color: #005DB8"></div>
                </div>
            </td>
        </tr>


    </table>


</header>
<footer >

    <div class="tabla_firma"  style="position:absolute;top:20px;">
        <table width="100%" border="0" cellspacing="">
            <tr>
                <td align="right">
                    {!! $sello !!}
                </td>
                <td align="left">
                    {!! $firma !!}
                </td>
            </tr>
        </table>
    </div>
    <div style="position:absolute;float:right;bottom:10px"><b>Page <span class="pagenum"></span></b></div>
    <div style=" padding:8px;margin-top:100px;width:100%">
        <div style="width:100%;height: 3px;background-color: #005DB8"></div>
    </div>
    <div class=" subtitle-header" style="text-align: center"> Doctor:
        <span>{{ $data->practitioner->name }} {{ $data->practitioner->surrname }}</span>
        | License Number:
        <span></span>
        @if($data->practitioner->qualifications()->first())
            <br>{{ $data->practitioner->qualifications()->first()->display }}
        @endif
    </div>

</footer>
<div class="content">
    <!--------------------- Aditional Info --------------------------------------------->
    @if($mode=="full")
        @include('consultations.consultation_report.consultation_report_block_aditional_info')
    @endif

    <!--------------------- Chief complaint --------------------------------------------->
    @include('consultations.consultation_report.consultation_report_block_chief_complaint')

    <!--------------------- Vital Signs ------------------------------------------------->
    @include('consultations.consultation_report.consultation_report_block_vital_signs')

    <!--------------------- Present Illness --------------------------------------------->
    @include('consultations.consultation_report.consultation_report_block_present_illness')

    <!--------------------- Medical history --------------------------------------------->
    @include('consultations.consultation_report.consultation_report_block_medical_history')

    <!--------------------- Social and family history ----------------------------------->
    @include('consultations.consultation_report.consultation_report_block_social_and_family_history')

    <!--------------------- Physical Exams --------------------------------------------->
    @include('consultations.consultation_report.consultation_report_block_physical_exams')

    <!--------------------- Disabilities ------------------------------------------------>
    @include('consultations.consultation_report.consultation_report_block_disabilities')



    <!--------------------- Multi type DME ------------------------------->
    <?php
    /*
    $data['multitypes_selecto'] = ['DME']; ?>
    @include('consultations.consultation_report.consultation_report_block_multi_types')


    <!--------------------- Therapy ----------------------------------------------------->
    @include('consultation.consultation_report.consultation_report_block_therapy')

    <!--------------------- Dmes -------------------------------------------------------->
    <!-- consultation.consultation_report.consultation_report_block_dmes (No en uso)-->

    <!--------------------- Multi type (Labs, Images ...) ------------------------------->
    */

    $data['multitypes_selecto'] = ['laboratory', 'images', 'procedure']; ?>
    @include('consultations.consultation_report.consultation_report_block_multi_types')


    <!--------------------- Reference to specialist ------------------------------------->
    @include('consultations.consultation_report.consultation_report_block_reference_to_specialist')


    <!--------------------- Medical Prescriptions --------------------------------------->
    @include('consultations.consultation_report.consultation_report_block_medical_prescriptions')

    {{--}}
    <!--------------------- Evaluacion -------------------------------------------------->
    @include('consultations.consultation_report.consultation_report_block_evaluacion')
    {{--}}

    <!--------------------- Multi type Injectable ------------------------------->
    {{--}}
    <?php
    $data['multitypes_selecto'] = ['injectable']; ?>
    @include('consultations.consultation_report.consultation_report_block_multi_types')



    <!--------------------- Plan -------------------------------------------------------->
    @include('consultations.consultation_report.consultation_report_block_plan')

    <!--------------------- General notes ----------------------------------------------->
    @include('consultations.consultation_report.consultation_report_block_general_notes')

    <!--------------------- Preguntas Urología - Índice Internacional de Síntomas Prostáticos ----------------------------------------------->
    @if($Test_Urologia_Resultado>-1)
        @include('consultations.consultation_report.consultation_urologia_index_internacional_sintomas_prostaticos', ['Test_Urologia_Resultado' => $Test_Urologia_Resultado])
    @endif
    {{--}}
</div>
<?php
function line_if_empty($value = "") {
    if ($value == "") {
        return "---";
    }

    if ($value == "-") {
        return "---";
    }

    return $value;
}

function list_array_as_table($array) {
    $html = '';
    $a = 0;

    foreach ($array as $item) {
        $a++;

        $html .= '<tr class="table-contents">';
        $html .= $item;
        $html .= '</tr>';
    }

    return $html;
}

function list_array($array) {
    $html = '<ul>';
    foreach ($array as $item) {
        $html .= '<li>'.$item."</li>";
    }

    $html .= '</ul>';
    return $html;
}

function get_from_array($list, $id = "") {
    if ($id == "") {
        return "";
    }

    if (isset($list[$id])) {
        return $list[$id];
    } else {
        return "";
    }
}

function gen($table, $horizontal = true){
    ?>

<table class="table" cellspacing="0" cellpadding="0">

    @if(isset($table['t-title']))
        <tr style="min-height: 50px">
            <td colspan="10" class="table-head">{{ $table['t-title'] }}</td>
        </tr>
    @endif


    @if($horizontal)
        <tr>
                <?php
            foreach ($table as $key => $item){
            if ($key != "t-title"){ ?>

            <td class="table-title">

                {{ $key }}
            </td>


                <?php
            }
            } ?>
        </tr>

        <tr>
                <?php
            foreach ($table as $key => $item){
            if ($key != "t-title"){ ?>

            <td class="table-value">
                {!! $item !!}
            </td>


                <?php
            }
            } ?>

        </tr>

    @endif


</table>

    <?php
}
?>
</body>
</html>
