<div id="marker-id-{{$section_id}}">
    <x-textarea-input   wire:model.live="general_note"  wire:keyup.debounce.800ms="save" id="general_note" class="mt-1 block w-full bottom-0" rows="2">{{$general_note}}</x-textarea-input>
    @include('partials.input_saving',['function'=>'save','saved'=>$saved])
</div>
