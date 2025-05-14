<div id="marker-id-4">
    @foreach($items as $vs)
        <div class="py-3">
            <x-input-label  :value="$vs->name.' ('.$vs->description.')'" />
            <x-textarea-input
                wire:model.live="values.{{$vs->code}}"
                class="mt-1 block w-full bottom-0" rows="2">{{$values[$vs->code]}}</x-textarea-input>
        </div>
    @endforeach
</div>
