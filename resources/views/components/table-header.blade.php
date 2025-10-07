<!-- Table Header -->
@php
if(!isset($show_create)) $show_create=true;
@endphp
<div class="page-table-header mb-2">
    <div class="row align-items-center">
        <div class="col">
            <div class="">
                <h3>{{ $title }}</h3>
                <div class="">
                    <div class="row">
                        <div class="col-12 col-sm-4 col-md-4 col-lg-4 col-xl-12">
                            <div class="top-nav-search table-search-blk">
                                <form action="javascript:;">
                                    <input type="text"  wire:model.live.debounce.300ms="search" placeholder="Buscar..." class="form-control" id="search">
                                    <a class="btn"><img src="{{ URL::asset('/assets/img/icons/search-normal.svg') }}"  alt=""></a>
                                </form>
                            </div>
                            @if($show_create)
                                <a href="{{ $li_1 }}" class="btn btn-primary submit-form add-pluss py-2" title="{{__('generic.new')}}" style="margin-left: 10px;margin-top: 15px;">
                                    {{--}} <i class="fa fa-plus" alt="{{__('generic.new')}}"></i> {{__('generic.new')}} {{--}}
                                    <i class="fa fa-plus me-1"></i>
                                    <span class="d-md-inline">{{__('generic.new')}}</span>
                                </a>
                            @endif
                            @if(isset($filters))
                                {{ $filters }}
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Table Header -->
