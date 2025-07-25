<x-app-layout>
    @section('css')
        <link href="{{url('styles/consultations.css?time2='.time())}}" rel="stylesheet" />
    @stop
    <livewire:consultation.create :encounter_id="$consultation->id"/>
</x-app-layout>
