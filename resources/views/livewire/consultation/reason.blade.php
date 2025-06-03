<div id="marker-id-{{$section_id}}">
      <x-textarea-input   wire:model.live="reason"
                          wire:keyup.debounce.800ms="save"
                          id="reason" class="mt-1 block w-full bottom-0" rows="2">{{$reason}}</x-textarea-input>

    @include('partials.input_saving',['function'=>'save','saved'=>$saved])

</div>
