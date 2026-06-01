<div id="marker-id-4">
    @foreach($items as $vs)
        <div class="input-block local-forms">
            <x-input-label  :value="$vs->name.' ('.$vs->description.')'" />
            <x-autosave-input
                type="textarea"
                :value="$values[$vs->code]"
                class="form-control mt-1 block w-full"
                rows="2"
                wire:model.live.debounce.500ms="values.{{$vs->code}}"
                wire:click="toggleInfo('{{ $vs->code }}')"
                placelholder="Ej : Normal"
                save-method="save"
                save-key="physical_exam_{{ $vs->code }}"
            />
            @if($activeInputCode === $vs->code && $suggestedAnswered)
                <div class="flex items-center text-blue-600 mt-2">
                    <div
                        wire:click="usarSugerencia('{{$vs->code}}')"
                        style="box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
                                width: 130px;
                                text-align: center;
                                padding: 2px;
                                background-color: #005dba;
                                color: #FFFFFF;
                                cursor: pointer;
                                margin-top: 15px;
                                margin-bottom: 5px;
                                margin-right:10px;
                                transition: 0.2s all;">
                        {{ __('general.use_suggestion') }}
                    </div>
                    <span class="text-sm font-medium">{{$suggestedAnswered}}</span>
                </div>
            @endif
        </div>
    @endforeach
</div>
