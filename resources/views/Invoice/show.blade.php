<?php $page = 'invoice-view'; ?>
@extends('layout.mainlayout')
@section('content')
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    {{ __('invoice.title') }}
                @endslot
                @slot('li_1')
                    {{ __('invoice.invoice_details') }}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            @include('partials.message')

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Invoice Header -->
                            <div class="invoice-head-clinic">
                                <div class="row">
                                    <div class="col-12 col-md-6">
                                        <div class="invoice-counts">
                                            <h4>{{ __('invoice.invoice') }} <span>#{{ $invoice->invoice_number }}</span></h4>
                                            <p class="text-muted mb-0">{{ __('invoice.identifier') }}: {{ is_array($invoice->identifier) ? implode(', ', $invoice->identifier) : $invoice->identifier }}</p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="invoice-counts float-end text-end">
                                            <h4>{{ __('invoice.statuss') }}:
                                                @php
                                                    $statusClasses = [
                                                        'draft' => 'status-gray',
                                                        'issued' => 'status-blue',
                                                        'sent' => 'status-orange',
                                                        'paid' => 'status-green',
                                                        'cancelled' => 'status-red',
                                                        'partially_paid' => 'status-yellow'
                                                    ];
                                                @endphp
                                                <a href="javascript:;" class="{{ $statusClasses[$invoice->status] ?? 'status-gray' }}">
                                                    {{ __('invoice.status.' . ($invoice->status ?? 'draft')) }}
                                                </a>
                                            </h4>
                                            @if($invoice->payment_status)
                                                <p class="text-muted mb-0">
                                                    {{ __('invoice.payment_statuss') }}:
                                                    <span class="badge badge-{{ $invoice->payment_status === 'paid' ? 'success' : ($invoice->payment_status === 'partial' ? 'warning' : 'danger') }}">
                                                        {{ __('invoice.payment_status.' . $invoice->payment_status) }}
                                                    </span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Organization and Patient Info -->
                            <div class="row custom-invoice">
                                <div class="col-12 col-md-6 m-b-20">
                                    @if($invoice->issuerOrganization || $invoice->client)
                                        @php $organization = $invoice->issuerOrganization ?? $invoice->client; @endphp
                                        <img src="{{ url('storage/'.$invoice->issuerOrganization->logo) }}" width="100%" alt="">
                                        <span>{{ $organization->name }}</span>

                                        <ul class="list-unstyled invoice-clinic mt-2">
                                            @if($invoice->encounter && $invoice->encounter->appointment->consultingRoom->branch->address)
                                                <li>{{ $invoice->encounter->appointment->consultingRoom->branch->address }}</li>
                                            @endif
                                            @if($organization->whatsapp)
                                                <li>{{ __('invoice.phone') }}: {{ $organization->whatsapp }}</li>
                                            @endif
                                            @if($organization->email)
                                                <li>{{ __('invoice.email') }}: {{ $organization->email }}</li>
                                            @endif
                                            @if($organization->tax_id)
                                                <li>{{ __('invoice.tax_id') }}: {{ $organization->tax_id }}</li>
                                            @endif
                                        </ul>
                                    @else
                                        <img src="{{ URL::asset('/assets/img/logo.png') }}" width="35" height="35" alt="">
                                        <span>{{ config('app.name', 'Meditech') }}</span>
                                        <ul class="list-unstyled invoice-clinic mt-2">
                                            <li>{{ __('invoice.organization_info') }}</li>
                                        </ul>
                                    @endif
                                </div>
                                <div class="col-12 col-md-6 m-b-20">
                                    <div class="invoice-details">
                                        <h3>{{ __('invoice.bill_to') }}:</h3>
                                        @if($invoice->patient)
                                            <h3>{{ $invoice->patient->name ?? ($invoice->patient->given_name . ' ' . $invoice->patient->family_name) }}</h3>
                                            <ul class="list-unstyled invoice-clinic">
                                                @if($invoice->patient->address)
                                                    <li>{{ $invoice->patient->address }}</li>
                                                @endif
                                                @if($invoice->patient->phone)
                                                    <li>{{ $invoice->patient->phone }}</li>
                                                @endif
                                                @if($invoice->patient->email)
                                                    <li>{{ $invoice->patient->email }}</li>
                                                @endif
                                                @if($invoice->patient->identifier)
                                                    <li>{{ __('invoice.patient_id') }}: {{ $invoice->patient->identifier }}</li>
                                                @endif
                                            </ul>
                                        @else
                                            <h3>{{ __('invoice.patient_not_available') }}</h3>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Invoice Dates and Info -->
                            <div class="row">
                                <div class="col-sm-6 col-lg-6 m-b-20">
                                    <div style="font-size: 14px;font-weight: 600;">
                                        <p>{{ __('invoice.invoice_date') }}: <i class="fa fa-calendar-check-o"></i>
                                            <span style="font-weight: 500;color: rgba(51, 53, 72, 0.5);">{{ $invoice->issue_date ? $invoice->issue_date->format('d M Y') : $invoice->created_at->format('d M Y') }}</span>
                                        </p>
                                        <p>{{ __('invoice.due_date') }}: <i class="fa fa-calendar-check-o"></i>
                                            <span style="font-weight: 500;color: rgba(51, 53, 72, 0.5);">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-6 m-b-20">
                                    <div class="invoice-details">
                                        @if($invoice->performerPractitioner)
                                            <h4>{{ __('invoice.practitioner') }}:
                                                <span>{{ $invoice->performerPractitioner->name ?? ($invoice->performerPractitioner->given_name . ' ' . $invoice->performerPractitioner->family_name) }}</span>
                                            </h4>
                                        @endif
                                        @if($invoice->encounter)
                                            <h4>{{ __('invoice.encounter_id') }}:
                                                <span>{{ $invoice->encounter->identifier }}</span>
                                            </h4>
                                        @endif
                                        @if($invoice->payment_terms)
                                            <h4>{{ __('invoice.payment_terms') }}:
                                                <span>{{ $invoice->payment_terms }}</span>
                                            </h4>
                                        @endif

                                    </div>
                                </div>
                            </div>

                            <!-- Line Items Table -->
                            <div class="table-responsive">
                                <table class="table table-hover border-0 custom-table invoice-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>{{ __('invoice.code') }}</th>
                                            <th>{{ __('invoice.service_description') }}</th>

                                            <th>{{ __('invoice.quantity') }}</th>
                                            <th>{{ __('invoice.unit_price') }}</th>
                                            <th>{{ __('invoice.line_total') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($invoice->lineItems as $index => $lineItem)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td class="text-center">{{ $lineItem->service_code ?? 'N/A' }}</td>
                                                <td>
                                                    <strong>{{ $lineItem->service_description ?? 'N/A' }}</strong>
                                                    @if($lineItem->chargeItem && $lineItem->chargeItem->note)
                                                        <br><small class="text-muted">{{ $lineItem->chargeItem->note }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ number_format($lineItem->quantity, 0) }}</td>
                                                <td>{{ $invoice->currency }} {{ number_format($lineItem->unit_price, 2) }}</td>
                                                <td>{{ $invoice->currency }} {{ number_format($lineItem->line_total_gross, 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fas fa-file-medical fa-2x mb-2"></i>
                                                    <br>{{ __('invoice.no_line_items') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Invoice Totals -->
                            <div>
                                <div class="row invoice-payment">
                                    <div class="col-sm-7">
                                        @if($invoice->note)
                                            <div class="invoice-info">
                                                <h5>{{ __('invoice.notes') }}</h5>
                                                <p class="text-muted">{{ $invoice->note }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-sm-5">
                                        <div class="m-b-20">
                                            <div class="table-responsive no-border">
                                                <table class="table mb-0 border-0 custom-table invoices-table total-fonts">
                                                    <tbody>
                                                        @if($invoice->subtotal_amount > 0)
                                                            <tr>
                                                                <td class="float-end">{{ __('invoice.subtotal') }}: {{ $invoice->currency }} {{ number_format($invoice->subtotal_amount, 2) }}</td>
                                                            </tr>
                                                        @endif
                                                        @if($invoice->tax_amount > 0)
                                                            <tr>
                                                                <td class="float-end">{{ __('invoice.tax') }} (7%): {{ $invoice->currency }} {{ number_format($invoice->tax_amount, 2) }}</td>
                                                            </tr>
                                                        @endif
                                                        <tr class="bold-total-invoice">
                                                            <td class="float-end">
                                                                <h5>{{ __('invoice.total_amount') }}: {{ $invoice->currency }} {{ number_format($invoice->total_amount, 2) }}</h5>
                                                            </td>
                                                        </tr>
                                                        @if($invoice->amount_paid > 0)
                                                            <tr>
                                                                <td class="float-end text-success">{{ __('invoice.amount_paid') }}: {{ $invoice->currency }} {{ number_format($invoice->amount_paid, 2) }}</td>
                                                            </tr>
                                                        @endif
                                                        @if($invoice->amount_due > 0)
                                                            <tr>
                                                                <td class="float-end text-danger">
                                                                    <strong>{{ __('invoice.amount_due') }}: {{ $invoice->currency }} {{ number_format($invoice->amount_due, 2) }}</strong>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Terms and Conditions -->
                                @if($invoice->terms_and_conditions || $invoice->description)
                                    <div class="invoice-info">
                                        <h5>{{ __('invoice.terms_and_conditions') }}</h5>
                                        <p class="text-muted">
                                            {{ $invoice->terms_and_conditions ?? $invoice->description ?? __('invoice.default_terms') }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Payment Information -->
                                @if($invoice->payment_date || $invoice->payment_method || $invoice->payment_reference)
                                    <div class="invoice-info">
                                        <h5>{{ __('invoice.payment_information') }}</h5>
                                        <div class="row">
                                            @if($invoice->payment_date)
                                                <div class="col-md-4">
                                                    <p><strong>{{ __('invoice.payment_date') }}:</strong><br>{{ $invoice->payment_date->format('d/m/Y') }}</p>
                                                </div>
                                            @endif
                                            @if($invoice->payment_method)
                                                <div class="col-md-4">
                                                    <p><strong>{{ __('invoice.payment_method') }}:</strong><br>{{ $invoice->payment_method }}</p>
                                                </div>
                                            @endif
                                            @if($invoice->payment_reference)
                                                <div class="col-md-4">
                                                    <p><strong>{{ __('invoice.payment_reference') }}:</strong><br>{{ $invoice->payment_reference }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="col-12">
                                <a href="{{ route('invoice.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>{{ __('invoice.back_to_list') }}
                                </a>
                                <div class="doctor-submit float-end">
                                    <a href="{{ route('invoice.download', $invoice->id) }}" target="_blank" class="btn btn-primary submit-form me-2">
                                        <i class="fas fa-download me-2"></i>{{ __('invoice.download_pdf') }}
                                    </a>
                                    <a href="{{ route('invoice.download', $invoice->id) }}?html=1" target="_blank" class="btn btn-outline-primary me-2">
                                        <i class="far fa-eye me-2"></i>{{ __('invoice.preview') }}
                                    </a>
                                    {{--}}
                                    <a href="javascript:window.print()" class="btn btn-outline-secondary">
                                        <i class="feather-printer me-2"></i>{{ __('invoice.print') }}
                                    </a>
                                    {{--}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>

    <style>
        .status-green { color: #28a745; }
        .status-blue { color: #007bff; }
        .status-orange { color: #fd7e14; }
        .status-red { color: #dc3545; }
        .status-gray { color: #6c757d; }
        .status-yellow { color: #ffc107; }

        .invoice-head-clinic {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .custom-invoice {
            margin-bottom: 30px;
        }

        .invoice-clinic li {
            color: #6c757d;
            font-size: 14px;
            line-height: 1.6;
        }

        .invoice-table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .invoice-table td {
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .total-fonts {
            font-size: 16px;
        }

        .bold-total-invoice {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .invoice-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        @media print {
            .doctor-submit,
            .page-header,
            .sidebar,
            .header {
                display: none !important;
            }
        }
    </style>
@endsection
