<a href="{{ route('consultation.index') }}" class="text-decoration-none">
    <div class="card patient-structure">
        <div class="card-body">
            @if($isLoading)
                <div class="loading-skeleton">
                    <div class="skeleton-title mb-2"></div>
                    <div class="skeleton-number mb-2"></div>
                    <div class="skeleton-percentage"></div>
                </div>
            @else
                <h5 class="text-base">Consultas  sin finalizar</h5>
                <h3>{{ $consultasEnProgreso }}<span class="{{ $statusClass }}"><img src="{{ URL::asset('/assets/img/icons/' . $icon) }}"
                            alt="" class="me-1">{{ number_format(abs($percentageChange), 1) }}%</span></h3>

            @endif
        </div>
        <style>
            .loading-skeleton {
                animation: pulse 1.5s ease-in-out infinite;
            }

            .skeleton-title {
                height: 20px;
                background: #e9ecef;
                border-radius: 4px;
                width: 80%;
            }

            .skeleton-number {
                height: 32px;
                background: #e9ecef;
                border-radius: 4px;
                width: 60%;
            }

            .skeleton-percentage {
                height: 16px;
                background: #e9ecef;
                border-radius: 4px;
                width: 40%;
            }

            @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.5; }
                100% { opacity: 1; }
            }

            .card:hover {
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                transform: translateY(-2px);
                transition: all 0.3s ease;
            }
        </style>
    </div>
</a>
