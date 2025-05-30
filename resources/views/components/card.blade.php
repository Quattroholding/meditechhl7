@php
if(!isset($show) or (request()->has('section') && request()->get('section') == $section_id)) $show='show';
$id = \Illuminate\Support\Str::uuid();
@endphp
<div class="card mb-1">
    <div class="card-header" id="{{$id}}">
        <h5 class="accordion-faq m-0">
            <a class="text-dark" data-bs-toggle="collapse" href="#section_marker_{{$section_id}}" aria-expanded="true">

                <h3 style="color: #fff;font-size: 15px;">  <i class="fa fa-plus"></i> {{__($title)}}</h3>
            </a>
        </h5>
    </div>
    <div id="section_marker_{{$section_id}}" class="collapse {{$show}}" aria-labelledby="{{$id}}" data-bs-parent="#accordion" style="">
        <div class="card-body">
            {{$card_body}}
        </div>
    </div>
</div>
