<div class="card top-departments">
                        <div class="card-header">
                            <h4 class="card-title mb-0" style="color: #fff;">{{__('Top Especialidades')}}</h4>
                        </div>
                        <div class="card-body">
                            @php
                                $json = file_get_contents(public_path('../public/assets/json/admin-dashboard-departments.json'));
                                $departments = json_decode($json, true);
                            @endphp
                            @foreach($top_specialties as $speciality)
                                <div class="activity-top">
                            
                                                <div class="activity-boxs comman-flex-center">
                                                    <img src="" alt="">
                                                </div>
                                                <div class="departments-list">
                                                    <h4>{!! \App\Models\MedicalSpeciality::where('id',$speciality->medical_speciality_id)->pluck('name')->first() !!}</h4>
                                                    <p>{{ $speciality->percentage}}%</p>
                                                </div>
                                            </div>
                            @endforeach
                            @foreach ($departments as $department)
                                @if ($department['name'] === 'Opthomology')
                                    <div class="activity-top mb-0">
                                        @else
                                            <div class="activity-top">
                                                @endif
                                                <div class="activity-boxs comman-flex-center">
                                                    <img src="{{ URL::asset('/assets/img/icons/' . $department['icon']) }}" alt="">
                                                </div>
                                                <div class="departments-list">
                                                    <h4>{{ $department['name'] }}</h4>
                                                    <p>{{ $department['percentage'] }}</p>
                                                </div>
                                            </div>
                                            @endforeach
                                    </div>
                        </div>
                    </div>
