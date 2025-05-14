<?php $page = 'error-404'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="error-box">
    <img class="img-fluid" src="{{URL::asset('/assets/img/error-01.png')}}" alt="Logo" >
    <h3><img class="img-fluid mb-0" src="{{URL::asset('/assets/img/icons/danger.svg')}}" alt="Logo">  Service Unavailable</h3>
    <p>You may have mistyped the address or the page may have moved.</p>
    <a href="{{url('/')}}" class="btn btn-primary go-home">Back to Home</a>
</div>
@endsection