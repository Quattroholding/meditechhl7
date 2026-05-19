<div class="">
    <div class="dash-widget">
        <div class="dash-boxs comman-flex-center">
            <img src="{{ $icon }}" alt="">
        </div>
        <div class="dash-content dash-count">
            <h4>{{ $title }}</h4>
            <h2>{{$symbol}}<span class="counter-up">{{ number_format($count, 2) }}</span></h2>
            <p><span class="{{$class }}"><i class="{{ $arrowClass }}"></i>{{ $change }}</span> {{__('generic.vs_last_month')}}</p>
        </div>
    </div>
</div>

