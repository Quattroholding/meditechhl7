<x-app-layout>
    @section('css')
        <link href="{{url('styles/consultations.css?time='.time())}}" rel="stylesheet" />
    @stop
    <div class="page-wrapper">
        <div class="content">
            <div class="col-md-10 col-sm-12" id="paciente">
                @include('consultations.partials.head',array('patient'=>$patient,'appointment'=>$appointment))
            </div>
            <div class="col-md-10  col-sm-12 my-4" id="reason">
                @component('components.card',array('title'=>'Queja Principal','show'=>''))
                    @slot('card_body')
                        <livewire:consultation.reason :encounter_id="$consultation->id"/>
                    @endslot
                @endcomponent
            </div>
            <div class="col-md-10  col-sm-12 my-3" id="vital_signs">
                @component('components.card',array('title'=>'Signos Vitales','show'=>''))
                    @slot('card_body')
                        <livewire:consultation.vital-signs :encounter_id="$consultation->id"/>
                    @endslot
                @endcomponent

            </div>
            <div class="col-md-10  col-sm-12 my-3" id="present_illness">
                @component('components.card',array('title'=>'Enfermedad Actual','show'=>''))
                    @slot('card_body')
                        <livewire:consultation.present-illness :encounter_id="$consultation->id"/>
                    @endslot
                @endcomponent
            </div>
            <div class="col-md-10  col-sm-12 my-3" id="physical-exam">
                @component('components.card',array('title'=>'Examen Fisico','show'=>''))
                    @slot('card_body')
                        <livewire:consultation.physical-exam :encounter_id="$consultation->id"/>
                    @endslot
                @endcomponent
            </div>
            <div class="col-md-10  col-sm-12 my-3" id="physical-exam">
                @component('components.card',array('title'=>'Diagnosticos','show'=>''))
                    @slot('card_body')
                        <livewire:consultation.diagnostics :encounter_id="$consultation->id"/>
                    @endslot
                @endcomponent
            </div>
            <div class="col-md-10  col-sm-12 my-3" id="physical-exam">
                @component('components.card',array('title'=>'Laboratorios','show'=>''))
                    @slot('card_body')
                        <livewire:consultation.service-request :encounter_id="$consultation->id" type="laboratory"/>
                    @endslot
                @endcomponent
            </div>
            <div class="col-md-10  col-sm-12 my-3" id="physical-exam">
                @component('components.card',array('title'=>'Imagenes','show'=>''))
                    @slot('card_body')
                        <livewire:consultation.service-request :encounter_id="$consultation->id" type="images"/>
                    @endslot
                @endcomponent
            </div>
            <div class="col-md-10  col-sm-12 my-3" id="physical-exam">
                @component('components.card',array('title'=>'Procedimientos','show'=>''))
                    @slot('card_body')
                        <livewire:consultation.service-request :encounter_id="$consultation->id" type="procedure"/>
                    @endslot
                @endcomponent
            </div>
            <div class="col-md-10  col-sm-12 my-3" id="physical-exam">
                @component('components.card',array('title'=>'Procedimientos (En sitio , aqui tenemos que ver si los sacamos de los cpt o creamos nuestra propia tabla)','show'=>''))
                    @slot('card_body')
                        <livewire:consultation.procedures :encounter_id="$consultation->id" type="procedure"/>
                    @endslot
                @endcomponent
            </div>
            <div class="col-md-10  col-sm-12 my-3" id="physical-exam">
                @component('components.card',array('title'=>'Referir especialista','show'=>''))
                    @slot('card_body')
                        <livewire:consultation.referral :encounter_id="$consultation->id"/>
                    @endslot
                @endcomponent
            </div>
            <div class="col-md-10  col-sm-12 my-3" id="physical-exam">
                @component('components.card',array('title'=>'Medicamentos','show'=>'show'))
                    @slot('card_body')
                        <livewire:consultation.medication-requests :encounter_id="$consultation->id" />
                    @endslot
                @endcomponent
            </div>
            <div class="my-6">&nbsp;</div>
            <div class="my-6">&nbsp;</div>
            <div class="my-6">&nbsp;</div>
            <div class="my-6">&nbsp;</div>
        </div>
    </div>
    @include('consultations.partials.side_menu')
    @include('consultations.partials.patient_info',array('id'=>$patient->id))
</x-app-layout>
