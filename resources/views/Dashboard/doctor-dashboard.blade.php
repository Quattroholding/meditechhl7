<x-app-layout>
    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard
                @endslot
                @slot('li_1')
                    Doctor Dashboard
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="good-morning-blk">
                <div class="row">
                    <div class="col-md-6">
                        <div class="morning-user">
                            <h2>{{__('generic.hello')}}, <span>Dr.{{auth()->user()->full_name}}</span></h2>
                            <p>{{__('generic.Have a nice day at work')}}</p>
                        </div>
                    </div>
                    <div class="col-md-6 position-blk">
                        <div class="morning-img">
                            <img src="{{ URL::asset('/assets/img/morning-img-02.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>

            <div class="doctor-list-blk">
                <div class="row">
                    @foreach ($dashboards as $dashboard)
                        <div class="col-xl-3 col-md-6">
                            <div class="doctor-widget border-right-bg">
                                <div class="doctor-box-icon flex-shrink-0">
                                    <img src="{{ URL::asset('/assets/img/icons/' . $dashboard['icon']) }}" alt="">
                                </div>
                                <div class="doctor-content dash-count flex-grow-1">
                                    <h4>
                                        @if($dashboard['title'] === 'Earnings')
                                        $<span
                                            class="counter-up">{{ $dashboard['count'] }}</span>
                                            @else
                                        <span
                                            class="counter-up">{{ $dashboard['count'] }}</span>
                                            @endif<span>{{ $dashboard['total'] }}</span><span
                                            class="{{ $dashboard['class'] }}">{{ $dashboard['percentageChange'] }}</span>
                                    </h4>
                                    <h5>{{ $dashboard['title'] }}</h5>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="row">
                {{--}}
                <div class="col-12 col-md-12 col-lg-12 col-xl-7">
                    <div class="card">
                        <div class="card-body">
                            <div class="chart-title patient-visit mb-0">
                                <h4>Income</h4>
                                <div class="income-value">
                                    <h3><span>$</span> 20,560</h3>
                                    <p><span class="passive-view"><i class="feather-arrow-up-right me-1"></i>40%</span> vs
                                        last month</p>
                                </div>
                                <div class="form-group mb-0">
                                   @livewire('doctor.select-dashboard')
                                </div>
                            </div>
                            <div id="apexcharts-area"></div>
                        </div>
                    </div>
                </div>
                {{--}}
                <div class="row">
                    <div class="col-lg-4">
                        @livewire('doctor.new-patients')
                    </div>
                    <div class="col-lg-4">
                        @livewire('doctor.old-patients')
                    </div>
                    <div class="col-lg-4">
                        @livewire('doctor.active-patients')
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        @livewire('doctor.next-appointment')
                        @livewire('doctor.patients-by-gender')
                    </div>
                    <div class="col-lg-9">
                        @livewire('doctor.completed-appointments')
                        @livewire('doctor.recent-appointment-list')
                    </div>
                </div>
            </div>
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
