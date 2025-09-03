@props(['route' => back()])
<!-- Page Header -->
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <div class="d-flex justify-content-between align-items-center">
                <ul class="breadcrumb mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ url($route) }}" class="text-base">{{ $title }} </a>
                    </li>
                    <li class="breadcrumb-item"><i class="fa fa-chevron-right text-base"></i></li>
                    <li class="breadcrumb-item active text-base">{{ $li_1 }}</li>
                </ul>
                
                @isset($actions)
                    <div class="page-header-actions">
                        {{ $actions }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>
<!-- /Page Header -->
@include('partials.message')
