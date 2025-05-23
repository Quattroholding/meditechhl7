<x-app-layout>
    @section('css')
        <link href="{{url('styles/consultations.css?time='.time())}}" rel="stylesheet" />
    @stop
    <div class="page-wrapper">
        <div class="content">
            <div class="col-md-10 col-sm-12" id="paciente">
                @include('consultations.partials.head',array('patient'=>$patient,'appointment'=>$appointment))
            </div>
            @foreach($encounter_sections as $section)
                <div class="col-md-10  col-sm-12 my-4" id="section_{{$section->id}}">
                    @component('components.card',array('title'=>$section->name_esp,'show'=>'','section_id'=>$section->id))
                        @slot('card_body')
                            @livewire($section->livewire_component_name, ['encounter_id' => $consultation->id,'section_id'=>$section->id])
                        @endslot
                    @endcomponent
                </div>
            @endforeach
            <div class="my-5">&nbsp;</div>
            <div class="my-5">&nbsp;</div>
        </div>
    </div>
    @include('consultations.partials.side_menu',array('appointment_id'=>$appointment->id))
    @include('consultations.partials.patient_info',array('id'=>$patient->id))
</x-app-layout>
