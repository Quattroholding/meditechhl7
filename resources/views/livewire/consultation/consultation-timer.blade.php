<div>
    @if($showTimer && !empty($timerData))
    <div style="padding: 0.5rem 0; display: flex; align-items: center; justify-content: flex-start;">
        {{--}}
        <i class="fas fa-clock" style="margin-right: 0.5rem; color: #6b7280; font-size: 1.1rem;"></i>
        {{--}}
        <div
            x-data="consultationTimer({{ json_encode($timerData) }})"
            x-init="init()"
            class="consultation-timer-v6"
            :class="{
                'timer-v6-green': timerData.color === 'green',
                'timer-v6-red': timerData.color === 'red'
            }"
        >
            <span class="timer-v6-label">{{ $timerData['message'] }}</span>
            <span class="timer-v6-separator">:</span>
            <span
                class="timer-v6-time"
                x-text="displayTime"
                title="{{ $timerData['status'] === 'finished' ? __('consultation.timer.tooltip_total_duration') : __('consultation.timer.tooltip_current_time') }}"
            ></span>
            @if(isset($timerData['overtime']))
                <span
                    class="timer-v6-badge"
                    title="{{ __('consultation.timer.tooltip_overtime') }}"
                >
                    +<span x-text="formatSeconds({{ $timerData['overtime'] }})"></span>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

<style>
.consultation-timer-v6 {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1rem;
    border-radius: 2rem;
    margin-left: 0.5rem;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.consultation-timer-v6:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.16);
}

.timer-v6-green {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border: 1px solid #10b981;
}

.timer-v6-red {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border: 1px solid #ef4444;
}

.timer-v6-label {
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.timer-v6-green .timer-v6-label {
    color: #065f46;
}

.timer-v6-red .timer-v6-label {
    color: #991b1b;
}

.timer-v6-separator {
    font-weight: 700;
    opacity: 0.5;
}

.timer-v6-green .timer-v6-separator {
    color: #065f46;
}

.timer-v6-red .timer-v6-separator {
    color: #991b1b;
}

.timer-v6-time {
    font-family: 'SF Mono', 'Monaco', 'Courier New', monospace;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.05em;
}

.timer-v6-green .timer-v6-time {
    color: #065f46;
}

.timer-v6-red .timer-v6-time {
    color: #991b1b;
}

.timer-v6-badge {
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.15rem 0.5rem;
    border-radius: 1rem;
    margin-left: 0.3rem;
}

.timer-v6-green .timer-v6-badge {
    background: rgba(6, 95, 70, 0.2);
    color: #065f46;
}

.timer-v6-red .timer-v6-badge {
    background: rgba(153, 27, 27, 0.2);
    color: #991b1b;
}
</style>

<script>
function consultationTimer(initialData) {
    return {
        timerData: initialData,
        displayTime: '00:00:00',
        interval: null,
        appointmentEndTimestamp: initialData.appointmentEndTimestamp || null,

        init() {
            this.updateDisplay();

            if (this.timerData.type === 'countdown' || this.timerData.type === 'countup') {
                this.startTimer();
            }
        },

        startTimer() {
            this.interval = setInterval(() => {
                this.updateDisplay();
            }, 1000);
        },

        calculateCurrentSeconds() {
            // Para consultas finalizadas (static), usar el valor inicial
            if (this.timerData.type === 'static') {
                return this.timerData.seconds || 0;
            }

            if (!this.appointmentEndTimestamp) {
                return 0;
            }

            const now = Math.floor(Date.now() / 1000);
            const diff = now - this.appointmentEndTimestamp;

            if (this.timerData.type === 'countdown') {
                const remaining = -diff;

                if (remaining <= 0) {
                    this.timerData.color = 'red';
                    this.timerData.type = 'countup';
                    this.timerData.message = '{{ __('consultation.timer.overtime') }}';
                    return Math.abs(diff);
                }

                return remaining;
            } else if (this.timerData.type === 'countup') {
                return Math.abs(diff);
            }

            return 0;
        },

        updateDisplay() {
            const seconds = this.calculateCurrentSeconds();
            this.displayTime = this.formatSeconds(seconds);
        },

        formatSeconds(totalSeconds) {
            const absSeconds = Math.floor(Math.abs(totalSeconds));
            const hours = Math.floor(absSeconds / 3600);
            const minutes = Math.floor((absSeconds % 3600) / 60);
            const seconds = absSeconds % 60;

            return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        },

        destroy() {
            if (this.interval) {
                clearInterval(this.interval);
            }
        }
    }
}
</script>
