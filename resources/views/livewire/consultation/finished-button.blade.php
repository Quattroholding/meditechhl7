<div>
    <!-- Botón Flotante de Dictado por Voz -->
    @livewire('consultation.voice-dictation-button', ['encounter_id' => $encounter_id])

    <form method="POST" action="{{ route('consultation.finished',$this->encounter->appointment_id) }}">
        @csrf
        <div class="text-end">
            <button type="submit" class="btn btn-success" id="finishedButton" @if(!$enabled) disabled @endif>{{ __('consultation.finished_button.finish_consultation') }}</button>
        </div>
    </form>
    @if(count($messages)>0)
    <div id="menu-right-info" class="menu-right-item">
        <div class="campos-obligatorios-label" style="">
            <b>{{ __('consultation.finished_button.add_required_info') }}</b>
        </div>
        <div class="btn-enviar-by-diagnostic-tester">
            <div class="campos-obligatorios-scroll">
                @foreach($messages as $index => $msg)
                    <div id="mandatory-{{$loop->index}}"
                         class="mandatory-field mandatory-empty"
                         data-section-id="{{ $messageSections[$index] ?? '' }}"
                         data-target-selector="{{ $messageTargets[$index] ?? '' }}"
                         onclick="scroll_mandatory(this)">
                        {{$msg}}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <style>
        @keyframes blink-red {
            0%, 100% {
                box-shadow: 0 0 0 0 rgba(255, 0, 0, 0);
                border: 2px solid transparent;
            }
            25% {
                box-shadow: 0 0 15px 5px rgba(255, 0, 0, 0.8);
                border: 2px solid #ff0000;
            }
            50% {
                box-shadow: 0 0 20px 8px rgba(255, 0, 0, 0.6);
                border: 2px solid #ff3333;
            }
            75% {
                box-shadow: 0 0 15px 5px rgba(255, 0, 0, 0.8);
                border: 2px solid #ff0000;
            }
        }

        .blink-red-highlight {
            animation: blink-red 2s ease-in-out 3;
            border-radius: 4px;
        }
    </style>

    <script>
        function scroll_mandatory(element) {
            const sectionId = element.getAttribute('data-section-id');
            const targetSelector = element.getAttribute('data-target-selector');

            if (!sectionId) {
                console.error('No section ID found for mandatory field');
                return;
            }

            // Find the accordion content element
            const accordionContent = document.querySelector(`[data-accordion-content="accordion-${sectionId}"]`);
            const accordionParent = document.getElementById(`parent_section_marker_${sectionId}`);

            if (!accordionContent || !accordionParent) {
                console.error(`Section with ID ${sectionId} not found`);
                return;
            }

            // Open the accordion if it's closed
            const wasHidden = accordionContent.classList.contains('hidden');
            if (wasHidden) {
                const accordionButton = accordionParent.querySelector('button');
                if (accordionButton) {
                    accordionButton.click();
                }
            }

            // Wait for accordion to open, then scroll to specific element
            setTimeout(() => {
                let targetElement = null;

                // Try to find the specific target element within the section
                if (targetSelector) {
                    const sectionContent = document.getElementById(`section_marker_${sectionId}`);
                    if (sectionContent) {
                        targetElement = sectionContent.querySelector(targetSelector);
                    }
                }

                // Fallback to section if specific element not found
                if (!targetElement) {
                    targetElement = accordionParent;
                }

                // Scroll to the target element
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Apply blink effect
                setTimeout(() => {
                    // Remove any existing blink animation
                    document.querySelectorAll('.blink-red-highlight').forEach(el => {
                        el.classList.remove('blink-red-highlight');
                    });

                    // Add blink animation to target
                    targetElement.classList.add('blink-red-highlight');

                    // Remove animation after it completes (2s * 3 iterations = 6s)
                    setTimeout(() => {
                        targetElement.classList.remove('blink-red-highlight');
                    }, 6000);
                }, 200);
            }, wasHidden ? 300 : 100);
        }
    </script>
</div>
