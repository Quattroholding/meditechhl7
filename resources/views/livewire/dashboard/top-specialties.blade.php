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

                        </div>
                    </div>
