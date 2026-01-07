<div>
    @if($currentReminder)
    <div style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 99999 !important; pointer-events: auto !important;">
        <!-- Floating Icon Button (always visible when there's a reminder) -->
        <div style="position: relative;">
            <!-- Panel expandido -->
            <div
                x-data="{ show: @entangle('showPanel') }"
                x-show="show"
                x-transition:enter="transition ease-out duration-200 transform origin-bottom-right"
                x-transition:enter-start="scale-75 opacity-0"
                x-transition:enter-end="scale-100 opacity-100"
                x-transition:leave="transition ease-in duration-150 transform origin-bottom-right"
                x-transition:leave-start="scale-100 opacity-100"
                x-transition:leave-end="scale-75 opacity-0"
                style="position: absolute; bottom: 70px; right: 0; width: 320px; margin-bottom: 8px; display: none;"
            >
                <div class="bg-white rounded-lg shadow-2xl border border-gray-200 overflow-hidden">
                    <!-- Header with gradient (color depends on required/suggestion) -->
                    <div class="px-4 py-3 flex items-center justify-between {{ $isRequired ? 'bg-gradient-to-r from-red-500 to-red-600' : 'bg-gradient-to-r from-blue-500 to-blue-600' }}">
                        <div class="flex items-center gap-2 flex-1">
                            <div class="flex-shrink-0">
                                @if($isRequired)
                                    <!-- Alert icon for required -->
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                @else
                                    <!-- Lightbulb icon for suggestion -->
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-black font-semibold text-sm leading-tight truncate text-center">
                                   {!! $reminderTitle !!}
                                </h4>
                                {{--}}
                                <p class="text-white/80 text-xs text-center mt-0.5">
                                    {{ $isRequired ? '⚠️ Requerido' : '💡 Sugerencia' }}
                                </p>
                                {{--}}
                            </div>
                        </div>

                        <button
                            wire:click="closePanel"
                            class="flex-shrink-0 ml-2 p-1 rounded-full bg-white/20 hover:bg-white/30 text-white transition-all bg-danger"
                            aria-label="Cerrar"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="px-4">
                        <p class="text-gray-700 text-xs leading-relaxed mb-3 text-center">
                            {{ $reminderMessage }}
                        </p>

                        @if($reminderActionUrl && $reminderActionText)
                        <div class="">
                            <a
                                href="{{ $reminderActionUrl }}" class="w-full btn btn-primary"
                                {{--}}class="w-full inline-flex items-center justify-center px-1 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-md transition-colors duration-150 shadow-sm hover:shadow"{{--}}
                            >
                                <i class="fa fa-plus"></i>
                                {{ $reminderActionText }}
                            </a>
                            <button
                                wire:click="dismiss"
                                class="w-full px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 text-xs font-medium transition-colors duration-150 rounded-md"
                            >
                                Descartar recordatorio
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- Progress indicator -->
                    <div class="px-4 pb-3">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div
                                    class="h-full bg-blue-500 transition-all duration-300 rounded-full"
                                    style="width: {{ $currentReminder === 'subscription' ? '14.28%' : ($currentReminder === 'branch' ? '28.57%' : ($currentReminder === 'consulting_room' ? '42.85%' : ($currentReminder === 'patient' ? '57.14%' : ($currentReminder === 'service_catalog' ? '71.42%' : ($currentReminder === 'working_hours' ? '85.71%' : '100%'))))) }}"
                                ></div>
                            </div>
                            <span class="text-xs text-gray-500 font-medium">
                                {{ $currentReminder === 'subscription' ? '1/7' : ($currentReminder === 'branch' ? '2/7' : ($currentReminder === 'consulting_room' ? '3/7' : ($currentReminder === 'patient' ? '4/7' : ($currentReminder === 'service_catalog' ? '5/7' : ($currentReminder === 'working_hours' ? '6/7' : '7/7'))))) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Floating Icon Button -->
            <button
                wire:click="togglePanel"
                style="position: relative; display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; border-radius: 50%; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); transition: all 0.2s;"
                class="bg-primary text-white hover:shadow-xl transform hover:scale-105 {{ $isRequired ? 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700' : 'bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700' }}"
                aria-label="Toggle reminders"
            >
                <!-- Notification badge (color depends on required/suggestion) -->
                <span style="position: absolute; top: -4px; right: -4px; display: flex; height: 20px; width: 20px;">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full  {{ $isRequired ? 'bg-red-400' : 'bg-yellow-500' }}"></span>
                    <span style="position: relative; display: inline-flex; border-radius: 50%; height: 20px; width: 20px; align-items: center; justify-content: center;" class="{{ $isRequired ? 'bg-red-500' : 'bg-blue-500' }}">
                        <span class="text-white text-xs font-bold">
                            {{ $currentReminder === 'subscription' ? '1' : ($currentReminder === 'branch' ? '2' : ($currentReminder === 'consulting_room' ? '3' : ($currentReminder === 'patient' ? '4' : ($currentReminder === 'service_catalog' ? '5' : ($currentReminder === 'working_hours' ? '6' : '7'))))) }}
                        </span>
                    </span>
                </span>

                <!-- Icon -->
                <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
            </button>
        </div>
    </div>
    @endif
</div>
