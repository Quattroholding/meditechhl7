@props(['route' => back()])
<!-- Page Header -->
<div class="page-header">
    <div class="row">
        <div class="col-sm-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ url($route) }}">{{ $title }} </a>
                </li>
                <li class="breadcrumb-item"><i class="fa fa-chevron-right"></i></li>
                <li class="breadcrumb-item active">{{ $li_1 }}</li>
            </ul>


        </div>
    </div>
</div>
<!-- /Page Header -->
@include('partials.message')
