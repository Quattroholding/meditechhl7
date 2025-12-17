<div id="marker-id-2">
    @foreach($items as $vs)
        <div class="input-block local-forms">
            <x-input-label  :value="$vs->name.' ('.$vs->default_unit.') '" />
            <x-autosave-input
                type="number"
                :value="$values[$vs->code]"
                wire:model.live.debounce.500ms="values.{{ $vs->code }}"
                save-method="saveValue"
                save-key="{{ $vs->code }}"
                class="form-control block w-full"
            />
        </div>
    @endforeach
</div>
