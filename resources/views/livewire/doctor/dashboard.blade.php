<div class="row">
    <div class="col-12 col-md-12  col-xl-7">
       <livewire:activity-chart />
        <div class="row">
            <div class="col-12 col-md-12  col-xl-8">
                @livewire('completed-appointments')
            </div>

            @livewire('next-appointment')
        </div>
    </div>
    @livewire('recent-appointment-list')
</div>
