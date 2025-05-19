<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="about-info">
                    <h4> {{__('patient.profile')}}<span><a href="javascript:;"><i class="feather-more-vertical"></i></a></span></h4>
                </div>
                <div class="doctor-profile-head">
                    <div class="row">
                        <div class="col-lg-6 col-xl-4 col-md-4">
                            <div class="profile-user-box">
                                <div class="profile-user-img">
                                    @if($data->avatar())
                                        <img src="{{url('storage/'.$data->avatar()->path) }}" alt="Profile">
                                    @else
                                        <img src="{{ URL::asset('/assets/img/profile-user-01.jpg') }}" alt="Profile">
                                    @endif


                                    <div class="form-group doctor-up-files profile-edit-icon mb-0">
                                        <div class="uplod d-flex">
                                            <label class="file-upload profile-upbtn mb-0">
                                                <img src="{{ URL::asset('/assets/img/icons/camera-icon.svg') }}"  alt="Profile"></i><input type="file">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="names-profiles">
                                    <h4>{!! $data->name !!}</h4>
                                    <h5>{{strtoupper($data->identifier_type)}} : {{$data->identifier}}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 d-flex align-items-center">
                            <div class="follow-group">
                                <div class="doctor-follows">
                                    <h5>Citas</h5>
                                    <h4>{{$data->appointments()->count()}}</h4>
                                </div>
                                <div class="doctor-follows">
                                    <h5>Diagnosticos</h5>
                                    <h4>{{$data->conditions()->count()}}</h4>
                                </div>
                                {{--}}
                                <div class="doctor-follows">
                                    <h5>Posts</h5>
                                    <h4>250</h4>
                                </div>
                                {{--}}
                            </div>
                        </div>
                        <div class="col-lg-6 col-xl-4 d-flex align-items-center">
                            <div class="follow-btn-group py-3">
                                <livewire:patient.add-medical-history :patient_id="$data->id">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
