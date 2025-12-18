<div id="marker-id-2">
    @foreach($items as $vs)
        <div class="input-block local-forms">
            <x-input-label  :value="$vs->name.' ('.$vs->default_unit.') '" />
            <x-autosave-input
                type="number"
                :value="$values[$vs->code]"
                wire:model.live.debounce.500ms="values.{{ $vs->code }}"
                onkeydown="return (event.keyCode !== 69 && event.keyCode !== 189 && event.keyCode !== 187)"
                save-method="saveValue"
                save-key="{{ $vs->code }}"
                :min_normal="$vs->min_normal_value"
                :max_normal="$vs->max_normal_value"
                class="form-control block w-full"
            />
            {{--}}
            @empty(!$vs->min_normal_value && !$vs->max_normal_value)
            <div class="pull-right float-right">
            Valores Normales :(Min:{{$vs->min_normal_value}} , Max :{{$vs->max_normal_value}})
            </div>
            @endempty
            {{--}}
        </div>
    @endforeach
</div>
