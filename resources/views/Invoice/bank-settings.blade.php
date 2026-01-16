<?php $page = 'bank-settings'; ?>
@extends('layout.mainlayout')
@section('content')
    <div class="page-wrapper">
        <!-- Page Content -->
        <div class="content container-fluid">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Invoice
                @endslot
                @slot('li_1')
                    Invoice Settings
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="row mt-4">
                <div class="col-xl-3 col-md-4">
                    <div class="widget settings-menu setting-list-menu">
                        <ul>
                            <li class="nav-item">
                                <a href="{{ url('invoices-settings') }}" class="nav-link ">
                                    <i class="fa fa-cog me-2"></i> <span>General Settings</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('tax-settings') }}" class="nav-link">
                                    <i class="fa fa-bookmark me-2"></i> <span>Tax Settings</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('bank-settings') }}" class="nav-link active">
                                    <i class="fas fa-university me-2"></i> <span>Bank Settings</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-xl-9 col-md-8">
                    <div class="card invoices-settings-card">
                        <div class="card-header px-3">
                            <h5 class="card-title">Bank Settings</h5>
                        </div>
                        <div class="card-body">

                            <!-- Form -->
                            <form action="#" class="invoices-settings-form">
                                <div class="row align-items-center form-group">
                                    <label for="name" class="col-sm-3 col-form-label input-label">Default Bank
                                        Account</label>
                                    <div class="col-sm-9">
                                        <label class="custom_check">
                                            <input type="checkbox" name="invoice">
                                            <span class="checkmark"></span> Check to enable Bank Account default
                                        </label>
                                    </div>
                                </div>
                                <div class="row align-items-center form-group">
                                    <label for="email" class="col-sm-3 col-form-label input-label">Account Holder
                                        Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="row align-items-center form-group">
                                    <label for="phone" class="col-sm-3 col-form-label input-label">Bank name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="row align-items-center form-group">
                                    <label for="phone" class="col-sm-3 col-form-label input-label">IFSC Code</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="row align-items-center form-group">
                                    <label for="phone" class="col-sm-3 col-form-label input-label">Account Number</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="invoice-setting-btn text-end">
                                    <button type="submit" class="btn cancel-btn me-2">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                            <!-- /Form -->

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Page Content -->
    </div>
@endsection
