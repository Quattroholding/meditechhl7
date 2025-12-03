{{-- Spatie Dashboard Component - Modified for Content Integration --}}
<div class="dashboard-container">
    <div class="dashboard-grid-container">
        {{ $slot }}
    </div>
</div>

<style>
/* Dashboard container styles */
.dashboard-container {
    width: 100%;
    position: relative;
}

.dashboard-grid-container {
    display: grid;
    grid-template-columns: repeat(12, 1fr);
    gap: 1rem;
    width: 100%;
}
</style>
