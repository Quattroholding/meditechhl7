<div>
<div id="marker-id-ipss">


    <!-- Score Display -->
    <div class="ipss-score-container">
        <div class="ipss-score-display">
            <span class="ipss-score-label">Score:</span>
            @php
                $severity = $this->getScoreSeverity();
                $severityClass = strtolower($severity['level']);
            @endphp
            <span class="ipss-score-badge {{ $severityClass }}">
                {{ $severity['level'] }}
            </span>
            <div class="ipss-score-bar-wrapper">
                <div class="ipss-score-bar-bg">
                    <div class="ipss-score-bar-fill {{ $severityClass }}" 
                         style="width: {{ min(($ipss_score / 35) * 100, 100) }}%"></div>
                    <span class="ipss-score-value">
                        {{ $ipss_score }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- IPSS Questions -->
    @foreach($questions as $index => $question)
        <div class="ipss-question-container" id="marker-id-ipss-{{ $index }}">
            <h4 class="ipss-question-title">
                {{ $question->question_esp }}
                <span class="ipss-question-subtitle">{{ $question->question_eng }}</span>
            </h4>

            @php
                $options_esp = json_decode($question->options_esp, true);
                $options_eng = json_decode($question->options_eng, true);
            @endphp

            <div class="ipss-options-grid">
                @foreach($options_esp as $optIndex => $option)
                    <button 
                        wire:click="saveAnswer({{ $question->id }}, '{{ $option }}')"
                        class="ipss-option-button @if(isset($answers[$question->id]) && $answers[$question->id] === $option) selected @endif"
                        type="button"
                    >
                        <div class="ipss-option-text">{{ $option }}</div>
                        @if(isset($options_eng[$optIndex]))
                            <div class="ipss-option-subtext">{{ $options_eng[$optIndex] }}</div>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>
    @endforeach

    <!-- Quality of Life Question -->
    <div class="ipss-qol-section">
        <h4 class="ipss-question-title">
            ¿Si tuviera que pasar el resto de su vida con su condición urinaria tal como está ahora, ¿cómo se sentiría al respecto?
            <span class="ipss-question-subtitle">If you were to spend the rest of your life with your urinary condition just the way it is now, how would you feel about that?</span>
        </h4>

        <div class="ipss-qol-grid">
            @php
                $qol_options = [
                    'Encantado' => 'Delighted',
                    'Complacido' => 'Pleased',
                    'Más satisfecho' => 'Mostly satisfied',
                    'Mixto: igualmente satisfecho/insatisfecho' => 'Mixed',
                    'Más bien insatisfecho' => 'Mostly dissatisfied',
                    'Descontento' => 'Unhappy',
                    'Terrible' => 'Terrible'
                ];
            @endphp

            @foreach($qol_options as $esp => $eng)
                <button 
                    wire:click="$set('quality_of_life', '{{ $esp }}')"
                    wire:key="qol-{{ $loop->index }}"
                    class="ipss-qol-button @if($quality_of_life === $esp) selected @endif"
                    type="button"
                >
                    <div class="ipss-option-text">{{ $esp }}</div>
                    <div class="ipss-option-subtext">{{ $eng }}</div>
                </button>
            @endforeach
        </div>

        <div class="ipss-qol-saving">
            @include('partials.input_saving', ['function' => 'saveQualityOfLife', 'saved' => $savedQualityOfLife])
        </div>
    </div>

    <!-- Score Legend -->
    <div class="ipss-legend-container">
        <h5 class="ipss-legend-title">Interpretación del Score:</h5>
        <div class="ipss-legend-items">
            <div class="ipss-legend-item">
                <div class="ipss-legend-color mild"></div>
                <span><strong>0-7:</strong> Leve (Mild)</span>
            </div>
            <div class="ipss-legend-item">
                <div class="ipss-legend-color moderate"></div>
                <span><strong>8-19:</strong> Moderado (Moderate)</span>
            </div>
            <div class="ipss-legend-item">
                <div class="ipss-legend-color severe"></div>
                <span><strong>20-35:</strong> Severo (Severe)</span>
            </div>
        </div>
    </div>
</div>
</div>