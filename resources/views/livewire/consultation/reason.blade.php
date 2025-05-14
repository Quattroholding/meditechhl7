<div  class="py-2">
    <h3>{{__('Queja Principal')}}</h3>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" >
        <span id="menu-marker-1" class="menu-maker"></span>
        <div class="p-6 text-gray-900" style="min-height: 100px;" id="marker-id-1">
            <x-textarea-input
                wire:model.live="reason"
                class="mt-1 block w-full bottom-0" rows="2">{{$reason}}</x-textarea-input>

        </div>
    </div>
</div>
