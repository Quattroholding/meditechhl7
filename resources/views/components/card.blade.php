@php
if(!isset($show)) $show='show';
$id = \Illuminate\Support\Str::uuid();
@endphp
<div class="card mb-1">
    <div class="card-header" id="headingOne">
        <h5 class="accordion-faq m-0">
            <a class="text-dark" data-bs-toggle="collapse" href="#{{$id}}" aria-expanded="true">

                <h3 style="color: #fff;font-size: 15px;">  <i class="fa fa-plus"></i> {{__($title)}}</h3>
            </a>
        </h5>
    </div>
    <div id="{{$id}}" class="collapse {{$show}}" aria-labelledby="headingOne" data-bs-parent="#accordion" style="">
        <div class="card-body">
            {{$card_body}}
        </div>
    </div>
</div>
