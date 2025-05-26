<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
    <div class="dash-widget">
        <div class="dash-boxs comman-flex-center">
            <img src="{{ $icon }}" alt="">
        </div>
        <div class="dash-content dash-count">
            <h4>{{ $title }}</h4>
            <h2><span class="counter-up">{{$symbol}}{{ $count }}</span></h2>
            <p><span class="{{$class }}"><i class="{{ $arrowClass }}"></i>{{ $change }}</span> {{__('generic.vs_last_month')}}</p>
        </div>
    </div>
</div>

