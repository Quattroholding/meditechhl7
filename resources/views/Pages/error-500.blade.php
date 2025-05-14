<?php $page = 'error-500'; ?>
@extends('layout.mainlayout')
@section('content')
<div class="error-box">
    <img class="img-fluid" src="{{URL::asset('/assets/img/error-02.png')}}" alt="Logo">
    <h3><img class="img-fluid mb-0" src="{{URL::asset('/assets/img/icons/danger.svg')}}" alt="Logo"> Internal Server Error</h3>
    <p>You do not have permission to view this resource</p>
    <a href="{{url('/')}}" class="btn btn-primary go-home">Go to Home</a>
</div>
@endsection