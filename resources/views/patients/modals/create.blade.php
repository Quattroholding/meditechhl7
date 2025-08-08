<x-modal :name="$name" maxWidth="2xl" focusable style="z-index: 10000">
    <div class="bg-white rounded-lg h-full overflow-auto relative z-10 " style="z-index: 10000">
        <div class="flex justify-between items-center p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">{{__('Registrar Paciente')}}</h2>
            <button x-on:click="$dispatch('close-modal', '{{ $name }}')"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto max-h-[calc(100vh-200px)]" >
            <!-- Tabs -->
            <livewire:patient.create/>
        </div>
    </div>
</x-modal>
