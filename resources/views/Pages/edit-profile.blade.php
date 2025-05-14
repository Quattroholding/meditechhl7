<?php $page = 'edit-profile'; ?>
@extends('layout.mainlayout')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard
                @endslot
                @slot('li_1')
                    Edit Profile
                @endslot
            @endcomponent
            <!-- /Page Header -->
            <form action="javascript:;">
                <div class="card-box">
                    <h3 class="card-title">Basic Informations</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="profile-img-wrap">
                                <img class="inline-block" src="{{ URL::asset('/assets/img/user.jpg') }}" alt="user">
                                <div class="fileupload btn">
                                    <span class="btn-text">edit</span>
                                    <input class="upload" type="file">
                                </div>
                            </div>
                            <div class="profile-basic">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group local-forms">
                                            <label class="focus-label">First Name</label>
                                            <input type="text" class="form-control floating" value="John">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group local-forms">
                                            <label class="focus-label">Last Name</label>
                                            <input type="text" class="form-control floating" value="Doe">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group local-forms ">
                                            <label class="focus-label">Birth Date</label>
                                            <div class="cal-icon">
                                                <input class="form-control floating datetimepicker" type="text"
                                                    value="05/06/1985">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group local-forms">
                                            <label class="focus-label">Gendar</label>
                                            {{-- <select class="form-control select">
                                                <option>Select Gendar</option>
                                                <option>Male</option>
                                                <option selected>Female</option>
                                            </select> --}}
                                            @livewire('select-gender')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-box">
                    <h3 class="card-title">Contact Informations</h3>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group local-forms">
                                <label class="focus-label">Address</label>
                                <input type="text" class="form-control floating" value="4487 Snowbird Lane">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">State</label>
                                <input type="text" class="form-control floating" value="New York">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Country</label>
                                <input type="text" class="form-control floating" value="United States">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Pin Code</label>
                                <input type="text" class="form-control floating" value="10523">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Phone Number</label>
                                <input type="text" class="form-control floating" value="631-889-3206">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-box">
                    <h3 class="card-title">Education Informations</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Institution</label>
                                <input type="text" class="form-control floating" value="Oxford University">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Subject</label>
                                <input type="text" class="form-control floating" value="Computer Science">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Starting Date</label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control floating datetimepicker" value="01/06/2002">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Complete Date</label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control floating datetimepicker" value="31/05/2023">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Degree</label>
                                <input type="text" class="form-control floating" value="BE Computer Science">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Grade</label>
                                <input type="text" class="form-control floating" value="Grade A">
                            </div>
                        </div>
                    </div>
                    <div class="add-more">
                        <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add More Institute</a>
                    </div>
                </div>
                <div class="card-box">
                    <h3 class="card-title">Experience Informations</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Company Name</label>
                                <input type="text" class="form-control floating" value="Digital Devlopment Inc">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Location</label>
                                <input type="text" class="form-control floating" value="United States">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Job Position</label>
                                <input type="text" class="form-control floating" value="Web Developer">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Period From</label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control floating datetimepicker"
                                        value="01/07/2023">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group local-forms">
                                <label class="focus-label">Period To</label>
                                <div class="cal-icon">
                                    <input type="text" class="form-control floating datetimepicker"
                                        value="08/06/2023">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="add-more">
                        <a href="javascript:;" class="btn btn-primary"><i class="fa fa-plus"></i> Add More Experience</a>
                    </div>
                </div>
                <div class="text-center ">
                    <button class="btn btn-primary submit-btn mb-4" type="button">Save</button>
                </div>
            </form>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
@endsection
