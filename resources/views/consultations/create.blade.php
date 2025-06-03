<x-app-layout>
    @section('css')
        <link href="{{url('styles/consultations.css?time2='.time())}}" rel="stylesheet" />
    @stop
    <div class="page-wrapper">
        <div class="content">
            <div class="col-md-10 col-sm-12" id="paciente">
                @include('consultations.partials.head',array('patient'=>$patient,'appointment'=>$appointment))
            </div>
            <div class="col-md-10 col-sm-12">
            <x-accordion>
                @foreach($encounter_sections as $section)
                    <div x-data="{ loaded: false }"
                         x-intersect="setTimeout(() => { loaded = true }, {{ $section->id * 200 }})"
                         class="min-h-[200px]">
                        <x-accordion-item data-id="{{$section->id}}" title="{{$section->name_esp}}" :isOpen="false" >
                        <template x-if="loaded">
                            <div x-transition:enter="transition ease-out duration-300">
                                @livewire($section->livewire_component_name, ['encounter_id' => $consultation->id,'section_id'=>$section->id,'section_name'=>$section->name_esp])
                            </div>
                        </template>
                            <template x-if="!loaded">
                                <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                                    <svg class="animate-spin h-8 w-8 text-blue-500" ...></svg>
                                </div>
                            </template>
                        </x-accordion-item>
                    </div>
                @endforeach
            </x-accordion>
            </div>
            <div class="my-5">&nbsp;</div>
            <div class="my-5">&nbsp;</div>
        </div>
    </div>
    @include('consultations.partials.side_menu',array('appointment_id'=>$appointment->id))
    @include('consultations.partials.patient_info',array('id'=>$patient->id))
</x-app-layout>
