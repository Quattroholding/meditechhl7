<div class="widget-skeleton">
    <div class="skeleton-header mb-3">
        <div class="skeleton-title"></div>
        <div class="skeleton-subtitle"></div>
    </div>
    
    @if($showChart)
        <div class="skeleton-chart mb-3"></div>
    @endif
    
    @for($i = 0; $i < $rows; $i++)
        <div class="skeleton-row mb-2">
            <div class="skeleton-text-full"></div>
            <div class="skeleton-text-half"></div>
        </div>
    @endfor
    
    <div class="skeleton-footer">
        <div class="skeleton-text-small"></div>
    </div>
</div>

<style>
.widget-skeleton {
    padding: 20px;
    animation: pulse 1.5s ease-in-out infinite;
}

.skeleton-header {
    text-align: center;
}

.skeleton-title {
    height: 20px;
    background: #e9ecef;
    border-radius: 4px;
    margin-bottom: 8px;
    width: 60%;
    margin: 0 auto 8px;
}

.skeleton-subtitle {
    height: 14px;
    background: #e9ecef;
    border-radius: 4px;
    width: 40%;
    margin: 0 auto;
}

.skeleton-chart {
    height: 150px;
    background: #e9ecef;
    border-radius: 8px;
}

.skeleton-row {
    display: flex;
    gap: 10px;
    align-items: center;
}

.skeleton-text-full {
    height: 16px;
    background: #e9ecef;
    border-radius: 4px;
    flex: 1;
}

.skeleton-text-half {
    height: 16px;
    background: #e9ecef;
    border-radius: 4px;
    width: 60px;
}

.skeleton-text-small {
    height: 12px;
    background: #e9ecef;
    border-radius: 4px;
    width: 80%;
    margin: 0 auto;
}

.skeleton-footer {
    text-align: center;
    margin-top: 15px;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
</style>