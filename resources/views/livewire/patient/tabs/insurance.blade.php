<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-white">
            <i class="feather-shield me-2 text-primary"></i>{{ __('patient.insurance.title') }}
        </h5>
        <span class="badge bg-info">{{ $insurancePolicies->total() }} {{ __('patient.insurance.total') }}</span>
    </div>
    <div class="card-body">
        @if($insurancePolicies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('patient.insurance.company') }}</th>
                            <th>{{ __('patient.insurance.policy_number') }}</th>
                            <th>{{ __('patient.insurance.priority') }}</th>
                            <th>{{ __('patient.insurance.holder') }}</th>
                            <th>{{ __('patient.insurance.relationship') }}</th>
                            <th>{{ __('patient.status') }}</th>
                            <th>{{ __('patient.insurance.validity') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($insurancePolicies as $policy)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $policy->insuranceCompany?->name ?? __('patient.insurance.no_company') }}</div>
                                @if($policy->group_number)
                                    <small class="text-muted">{{ __('patient.insurance.group') }}: {{ $policy->group_number }}</small>
                                @endif
                            </td>
                            <td>
                                <code>{{ $policy->policy_number }}</code>
                                @if($policy->subscriber_id)
                                    <br><small class="text-muted">ID: {{ $policy->subscriber_id }}</small>
                                @endif
                            </td>
                            <td>
                                @switch($policy->priority)
                                    @case('primary')
                                        <span class="badge bg-primary">{{ __('patient.insurance.primary') }}</span>
                                        @break
                                    @case('secondary')
                                        <span class="badge bg-info">{{ __('patient.insurance.secondary') }}</span>
                                        @break
                                    @case('tertiary')
                                        <span class="badge bg-secondary">{{ __('patient.insurance.tertiary') }}</span>
                                        @break
                                    @default
                                        <span class="badge bg-light text-dark">{{ ucfirst($policy->priority) }}</span>
                                @endswitch
                            </td>
                            <td>
                                @if($policy->subscriberPatient)
                                    <div class="fw-medium">{{ $policy->subscriberPatient->name }}</div>
                                    <small class="text-muted">{{ $policy->subscriberPatient->identifier }}</small>
                                @else
                                    <div class="fw-medium">{{ $policy->subscriber_name ?: __('patient.info.not_specified') }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($policy->relationship_to_subscriber) }}</span>
                            </td>
                            <td>
                                @if($policy->isActive())
                                    <span class="badge bg-success">{{ __('patient.insurance.active') }}</span>
                                @elseif($policy->isExpired())
                                    <span class="badge bg-danger">{{ __('patient.insurance.expired') }}</span>
                                @else
                                    <span class="badge bg-warning">{{ __('patient.insurance.inactive') }}</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $policy->effective_date->format('d/m/Y') }}</div>
                                @if($policy->expiration_date)
                                    <small class="text-muted">{{ __('patient.insurance.until') }} {{ $policy->expiration_date->format('d/m/Y') }}</small>
                                @else
                                    <small class="text-success">{{ __('patient.insurance.no_expiration') }}</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <small class="text-muted">
                        {{ __('patient.insurance.showing') }} {{ $insurancePolicies->firstItem() }} {{ __('generic.to') }} {{ $insurancePolicies->lastItem() }}
                        {{ __('generic.from') }} {{ $insurancePolicies->total() }} {{ __('patient.insurance.policies') }}
                    </small>
                </div>
                @if($insurancePolicies->hasMorePages())
                    <button wire:click="loadMore" class="btn btn-outline-primary btn-sm">
                        <i class="feather-plus me-1"></i>{{ __('patient.insurance.load_more') }}
                    </button>
                @endif
            </div>

            <!-- Coverage Summary -->
            @if($insurancePolicies->where('is_active', true)->count() > 0)
                <div class="alert alert-info mt-3">
                    <h6><i class="feather-info me-2"></i>{{ __('patient.insurance.active_coverage_summary') }}</h6>
                    <div class="row">
                        @foreach($insurancePolicies->where('is_active', true) as $policy)
                            <div class="col-md-6 mb-2">
                                <strong>{{ $policy->priority === 'primary' ? __('patient.insurance.primary_insurance') : __('patient.insurance.secondary_insurance') }}</strong>
                                <br>{{ __('patient.insurance.coverage') }}: {{ $policy->coverage_percentage }}%
                                @if($policy->copay_amount > 0)
                                    <br>{{ __('patient.insurance.copay') }}: ${{ number_format($policy->copay_amount, 2) }}
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-4">
                <div class="empty-state">
                    <i class="feather-shield text-muted" style="font-size: 3rem;"></i>
                    <h5 class="mt-3 text-muted">{{ __('patient.insurance.no_insurance') }}</h5>
                    <p class="text-muted">{{ __('patient.insurance.no_insurance_message') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
