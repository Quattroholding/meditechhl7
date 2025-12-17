<div id="marker-id-{{$section_id}}">
    <x-autosave-input
        type="textarea"
        :value="$general_note"
        class="form-control mt-1 block w-full"
        rows="2"
        wire:model.live.debounce.500ms="general_note"
        save-method="save"
        save-key="general_note"
    />
</div>
