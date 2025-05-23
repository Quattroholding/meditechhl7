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
                @foreach($data->qualifications()->get() as $q)
                    <div class="personal-activity">
                        <div class="personal-icons status-grey">
                            <img src="{{ URL::asset('/assets/img/icons/medal-01.svg') }}"
                                 alt="">
                        </div>
                        <div class="views-personal">
                            <h4>{{$q->display}}</h4>
                            <h5>{{__('doctor.period')}} : de {{$q->period_start}} a {{$q->period_end}}</h5>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
