<div class="col-lg-4">
    <div class="doctor-personals-grp">
        <div class="card">
            <div class="card-body">
                <div class="heading-detail ">
                    <h4 class="mb-3">{{__('doctor.about_me')}}</h4>
                </div>
                <div class="about-me-list">
                    <ul class="list-space">
                        <li>
                            <h4>{{__('doctor.gender')}}</h4>
                            <span>{{$data->gender}}</span>
                        </li>
                        <li>
                            <h4>{{__('doctor.birthdate')}}</h4>
                            <span>{{$data->birth_date}}</span>
                        </li>
                        <li>
                            <h4>{{__('doctor.email')}}</h4>
                            <span>{{$data->email}}</span>
                        </li>
                        <li>
                            <h4>{{__('doctor.phone')}}</h4>
                            <span>{{$data->phone}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="doctor-personals-grp">
        <div class="card">
            <div class="card-body">
                <div class="heading-detail">
                    <h4>{{__('doctor.qualifications')}}</h4>
                </div>
                @foreach($qualifications as $q)
                    <div class="row">

                        <div class="col-md-7">
                            <div class="personal-activity">
                                <div class="personal-icons  {{ $q->default ? 'status-blue' : 'status-grey' }}">
                                    <img src="{{ URL::asset('/assets/img/icons/medal-01.svg') }}"   alt="">
                                </div>
                                <div class="views-personal">
                                    <h4>{{$q->display}}</h4>
                                    <h5>{{__('doctor.period')}} : <br/> de {{$q->period_start}} a {{$q->period_end}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="flex items-center">
                                <div wire:click="setDefaultSpecialty({{ $q->id }})"
                                     class="toggle-switch {{ $q->default ? 'active' : '' }}">
                                    <div class="toggle-thumb"></div>
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-700">
                                    @if($q->default)
                                        {{__('Principal')}}
                                    @else
                                        {{__('Establecer')}}<br/>{{__('como principal')}}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
                @if($qualifications->count() > 0)
                    <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-amber-600 mt-0.5 mr-2"></i>
                            <div class="text-sm text-amber-800">
                                <p class="font-medium">¿Qué es la especialidad principal?</p>
                                <p>Es la especialidad que aparecerá destacada en tu perfil y será lo primero que vean los pacientes al buscar médicos.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <style>
        .toggle-switch {
            position: relative;
            width: 44px;
            height: 24px;
            background-color: #e5e7eb;
            border-radius: 12px;
            transition: background-color 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
        }

        .toggle-switch.active {
            background-color: #003b62;
        }

        .toggle-switch .toggle-thumb {
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 50%;
            position: absolute;
            top: 2px;
            left: 2px;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .toggle-switch.active .toggle-thumb {
            transform: translateX(20px);
        }

        .toggle-switch:hover {
            background-color: #d1d5db;
        }

        .toggle-switch.active:hover {
            background-color: #003b62;
        }
    </style>
</div>
