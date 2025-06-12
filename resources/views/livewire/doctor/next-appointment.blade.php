<div class="col-12 col-md-12  col-xl-12 d-flex">
    <div class="card wallet-widget">
        <div class="circle-bar circle-bar2">
            <div class="circle-graph2" data-percent="{{ $timeRemainingPercentage }}">
                <b><img src="{{ URL::asset('/assets/img/icons/timer.svg') }}" alt=""></b>
            </div>
        </div>
        <div class="main-limit">
            <p>Next Appointment</p>
            <h4>{{ $nextAppointmentTime }}</h4>
        </div>
    </div>
</div>
