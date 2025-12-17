<div id="marker-id-{{$section_id}}">
    <x-autosave-input
        type="textarea"
        :value="$reason"
        class="form-control mt-1 block w-full"
        rows="2"
        wire:model.live.debounce.500ms="reason"
        save-method="save"
        save-key="reason"
    />

</div>
