<div id="marker-id-{{$section_id}}">
    @component('components.card',array('title'=>$section_name,'show'=>'','section_id'=>$section_id))
        @slot('card_body')
            <x-textarea-input   wire:model.live="reason"   class="mt-1 block w-full bottom-0" rows="2">{{$reason}}</x-textarea-input>
        @endslot
    @endcomponent
</div>
