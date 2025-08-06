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
                    <div class="row g-2 align-items-center space-x-2">
                        <div class="col-12 col-sm-7 col-md-7 col-lg-7 col-xl-7">
                            <div class="top-nav-search table-search-blk w-100">
                                <form action="javascript:;" class="w-100">
                                    <input type="text"  wire:model.live.debounce.300ms="search" placeholder="Buscar..." class="form-control" id="search">
                                    <a class="btn">{{--}}<img src="{{ URL::asset('/assets/img/icons/search-normal.svg') }}"  alt="">{{--}}</a>
                                </form>
                            </div>
                        </div>
                        @if($show_create)
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-4">
                            <div class="d-grid d-sm-block text-sm-end">
                                <a href="{{ $li_1 }}" class="btn btn-primary submit-form add-pluss py-2" title="{{__('generic.new')}}">
                                   {{--}} <i class="fa fa-plus" alt="{{__('generic.new')}}"></i> {{__('generic.new')}} {{--}}
                                    <i class="fa fa-plus me-1"></i>
                                    <span class="d-md-inline">{{__('generic.new')}}</span>
                                </a>
                                {{--}}
                                <a href="#" class="btn btn-light doctor-refresh ms-2" title="{{__('generic.refresh')}}"><img src="{{ URL::asset('/assets/img/icons/re-fresh.svg') }}" alt=""></a>
                                {{--}}
                            </div>
                        </div>
                        @endif
                    </div>
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
