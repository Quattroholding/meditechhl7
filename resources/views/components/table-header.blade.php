<!-- Table Header -->
@php
if(!isset($show_create)) $show_create=true;
@endphp
<div class="page-table-header mb-2">
    <div class="row align-items-center">
        <div class="col">
            <div class="doctor-table-blk">
                <h3>{{ $title }}</h3>
                <div class="doctor-search-blk">
                    <div class="top-nav-search table-search-blk">
                        <form action="javascript:;">
                            <input type="text"  wire:model.live.debounce.300ms="search" placeholder="Buscar..." class="form-control" id="search">
                            <a class="btn"><img src="{{ URL::asset('/assets/img/icons/search-normal.svg') }}"  alt=""></a>
                        </form>
                    </div>
                    @if($show_create)
                    <div class="">
                        <a href="{{ $li_1 }}" class="btn btn-primary submit-form me-2 add-pluss ms-2 py-2" title="{{__('generic.new')}}">

                            <i class="fa fa-plus" alt="{{__('generic.new')}}"></i> {{__('generic.new')}}
                        </a>
                        {{--}}
                        <a href="#" class="btn btn-light doctor-refresh ms-2" title="{{__('generic.refresh')}}"><img src="{{ URL::asset('/assets/img/icons/re-fresh.svg') }}" alt=""></a>
                        {{--}}
                    </div>
                    @endif
                </div>
            </div>
        </div>
        {{--}}
        <div class="col-auto text-end float-end ms-auto download-grp">
            <a href="javascript:;" class=" me-2"><img src="{{ URL::asset('/assets/img/icons/pdf-icon-01.svg') }}"  alt=""></a>
            <a href="javascript:;" class=" me-2"><img src="{{ URL::asset('/assets/img/icons/pdf-icon-02.svg') }}"  alt=""></a>
            <a href="javascript:;" class=" me-2"><img src="{{ URL::asset('/assets/img/icons/pdf-icon-03.svg') }}"  alt=""></a>
            <a href="javascript:;"><img src="{{ URL::asset('/assets/img/icons/pdf-icon-04.svg') }}" alt=""></a>
        </div>
        {{--}}
    </div>
</div>
<!-- /Table Header -->
