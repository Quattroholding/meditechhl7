<div id="marker-id-2">
    @foreach($items as $vs)
        <div class="py-3">
            <x-input-label  :value="$vs->name.' ('.$vs->default_unit.') '" />
            <x-text-input class="block w-full" wire:model.live="values.{{$vs->code}}" :value="$values[$vs->code]" type="number"/>

            <div class="text-sm text-gray-500 mt-1">
                {{__('Valores Normales')}} : Min {{$vs->min_normal_value}} , Max {{$vs->max_normal_value}}
            </div>
        </div>
    @endforeach
</div>
