<x-app-layout>
    @section('css')
        <link href="{{url('styles/consultations.css?time='.time())}}" rel="stylesheet" />
    @stop
    <div class="page-wrapper">
        <div class="content">
            <div class="col-md-10 col-sm-12" id="paciente">
                @include('consultations.partials.head',array('patient'=>$patient,'appointment'=>$appointment))
            </div>
            <div id="accordion-collapse" data-accordion="collapse">
                @foreach($encounter_sections as $section)
                <h2 id="accordion-collapse-heading-{{$section->id}}">
                    <button type="button" class="flex items-center justify-between w-full p-5 font-medium rtl:text-right text-gray-500 border border-b-0 border-gray-200 rounded-t-xl focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-800 dark:border-gray-700 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 gap-3"
                            data-accordion-target="#accordion-collapse-body-{{$section->id}}"
                            aria-expanded="true"
                            aria-controls="accordion-collapse-body-{{$section->id}}">
                        <span><i class="fa fa-plus"></i> {{__($section->name_esp)}}</span>
                        <svg data-accordion-icon class="w-3 h-3 rotate-180 shrink-0" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5 5 1 1 5"/>
                        </svg>
                    </button>
                </h2>
                <div id="accordion-collapse-body-{{$section->id}}" class="hidden" aria-labelledby="accordion-collapse-heading-{{$section->id}}">
                    @livewire($section->livewire_component_name, ['encounter_id' => $consultation->id,'section_id'=>$section->id,'section_name'=>$section->name_esp])
                </div>
                @endforeach
            </div>
            <div class="my-5">&nbsp;</div>
            <div class="my-5">&nbsp;</div>
        </div>
    </div>
    @include('consultations.partials.side_menu',array('appointment_id'=>$appointment->id))
    @include('consultations.partials.patient_info',array('id'=>$patient->id))
</x-app-layout>
