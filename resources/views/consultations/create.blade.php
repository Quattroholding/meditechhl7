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
                <div class="col-md-10  col-sm-12 my-4" id="reason">
                    @component('components.card',array('title'=>$section->name_esp,'show'=>'','section_id'=>$section->id))
                        @slot('card_body')
                            @livewire($section->livewire_component_name, ['encounter_id' => $consultation->id,'section_id'=>$section->id])
                        @endslot
                    @endcomponent
                </div>
            @endforeach
        </div>
    </div>
    @include('consultations.partials.side_menu')
    @include('consultations.partials.patient_info',array('id'=>$patient->id))
</x-app-layout>
