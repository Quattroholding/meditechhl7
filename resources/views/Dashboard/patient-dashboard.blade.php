<?php $page = 'patient-dashboard'; ?>
<x-app-layout>

    <div class="page-wrapper">
        <div class="content">
            <!-- Page Header -->
            @component('components.page-header')
                @slot('title')
                    Dashboard
                @endslot
                @slot('li_1')
                    {{__('patient.title')}}
                @endslot
            @endcomponent
            <!-- /Page Header -->

            <div class="good-morning-blk">
                <div class="row">
                    <div class="col-md-6">
                        <div class="morning-user">
                            <h2>{{__('generic.hello')}}, <span>{{auth()->user()->patient->name}}</span></h2>
                            <p>{{ auth()->user()->patient->identifier_type }}: {{ auth()->user()->patient->identifier }} •
                            @if(auth()->user()->patient->age) {{ auth()->user()->patient->age }} años • @endif
                                {{ ucfirst(__('patient.'.auth()->user()->patient->gender) ?? 'No especificado') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 position-blk">
                        <div class="morning-img">
                            <img src="{{ URL::asset('/assets/img/morning-img-03.png') }}" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-12 col-lg-12 col-xl-12">
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
                    <div class="col-12 col-md-12  col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title d-inline-block" style="color: #fff">{{__('Ultimas 5 Citas')}}</h4>
                                <a href="{{ url('appointments') }}" class="patient-views float-end" style="color: #fff">{{__('ver todas')}}</a>
                            </div>
                            <div class="card-body p-0 table-dash">
                                <livewire:appointment.data-table pagination="-1"
                                                                 sortDirecction="desc"
                                                                 :limit="5"
                                                                 :show_create="false"
                                                                 wire:key="{{\Illuminate\Support\Str::random(5)}}"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">

                    </div>
                    <!-- Con parámetros personalizados -->
                    {{--}}
                    <livewire:patient.dashboard.vital-signs-status
                        :patient-id="auth()->user()->patient->id"
                        :selected-period="30"
                        :auto-refresh="true" />

                    <div class="card">
                        <div class="card-body">
                            <div class="chart-title patient-visit mb-0">
                                <h4>Static of your Health</h4>
                                <div class="income-value">
                                    <p><span class="passive-view"><i class="feather-arrow-up-right me-1"></i>40%</span> vs
                                        last month</p>
                                </div>
                                <div class="average-health">
                                    <h5>72bmp <span>Average</span></h5>
                                </div>
                                <div class="form-group mb-0">
                                    @livewire('select-dashboard')
                                </div>
                            </div>
                            <div id="health-chart"></div>
                        </div>
                    </div>
                    {{--}}
                </div>
                {{--}}
                <div class="col-12 col-md-12 col-lg-12 col-xl-5 d-flex">
                    <div class="card">
                        <div class="card-body">
                            <div class="chart-title patient-visit">
                                <h4>{{__('Indice de mas corporal')}}</h4>
                            </div>
                            <div class="body-mass-blk">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="weight-blk">
                                            <div class="center slider">
                                                <div>
                                                    <h4>68</h4>
                                                    <span>kg</span>
                                                </div>
                                                <div>
                                                    <h4>70</h4>
                                                    <span>kg</span>
                                                </div>
                                                <div>
                                                    <h4>72</h4>
                                                    <span>kg</span>
                                                </div>
                                                <div>
                                                    <h4>74</h4>
                                                    <span>kg</span>
                                                </div>
                                                <div>
                                                    <h4>76</h4>
                                                    <span>kg</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="weight-blk">
                                            <div class="center slider">
                                                <div>
                                                    <h4>160</h4>
                                                    <span>cm</span>
                                                </div>
                                                <div>
                                                    <h4>162</h4>
                                                    <span>cm</span>
                                                </div>
                                                <div>
                                                    <h4>164</h4>
                                                    <span>cm</span>
                                                </div>
                                                <div>
                                                    <h4>166</h4>
                                                    <span>cm</span>
                                                </div>
                                                <div>
                                                    <h4>168</h4>
                                                    <span>cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress weight-bar">
                                    <div class="progress-bar progress-bar-success" role="progressbar"></div>
                                </div>
                                <ul class="weight-checkit">
                                    <li>Underweight</li>
                                    <li>Normal (45.5)</li>
                                    <li>Overweight</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                {{--}}
            </div>
            {{--}}
            <livewire:patient.dashboard-chat/>
            <livewire:patient.dashboard-history/>
            {{--}}
        </div>
        @component('components.notification-box')
        @endcomponent
    </div>
</x-app-layout>
