<div>
    <form method="POST" action="{{ route('consultation.finished', $this->encounter->appointment_id) }}">
        @csrf
        <button type="submit"
                class="btn-consultation btn-consultation-finish @if($enabled) active @endif"
                id="finishedButton"
                @if(!$enabled) disabled @endif>
            <i class="fas fa-check-circle"></i>
            {{ __('consultation.finished_button.finish_consultation') }}
        </button>
    </form>
</div>
