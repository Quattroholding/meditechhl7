<div class="col-xl-12">
    <div class="accordion custom-accordion mb-3" id="custom-accordion-one">
        <div class="card mb-1">
            <div class="card-header" id="headingNine">
                <h5 class="accordion-faq m-0 position-relative">
                    <a class="custom-accordion-title text-white d-block collapsed" data-bs-toggle="collapse" href="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                        {{__('encounter.titles')}}
                    </a>
                </h5>
            </div>

            <div id="collapseNine" class="collapse" aria-labelledby="headingFour" data-bs-parent="#custom-accordion-one" style="">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">Id</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">{{__('encounter.practitioner')}} </th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">{{__('encounter.status')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">{{__('encounter.speciality')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">{{__('encounter.day')}} {{__('encounter.time')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">{{__('encounter.reason')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($encounters as $dato)
                                <tr>
                                    <td>{{$dato->id}}</td>
                                    <td>{!!  $dato->practitioner->name !!} </td>
                                    <td>{!!  $dato->status !!}</td>
                                    <td>{{$dato->medicalSpeciality->name}}</td>
                                    <td>{{ \Carbon\Carbon::parse($dato->start)->format('d-m-Y')  }} {{ $dato->time }}</td>
                                    <td>{{$dato->reason}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-1" wire:click="vitalSigns()">
            <div class="card-header" id="headingFive">
                <h5 class="accordion-faq m-0 position-relative">
                    <a class="custom-accordion-title text-white d-block collapsed" data-bs-toggle="collapse" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                        {{__('consultation.vital_signs')}}
                    </a>
                </h5>
            </div>
            <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-bs-parent="#custom-accordion-one" style="">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">{{__('encounter.title')}}</th>
                                @foreach(\App\Models\ClinicalObservationType::whereCategory('vital_sign')->get() as $cot)
                                    <th class="border-b border-gray-300 p-2 cursor-pointer" title="{{$cot->name}}">{{$cot->default_unit}}</th>
                                @endforeach
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($vital_signs as $vs)
                                <tr>
                                    <td>{{$dato->id}}</td>
                                    @foreach(\App\Models\ClinicalObservationType::whereCategory('vital_sign')->get() as $cot)
                                        <td>
                                            @if($vs->where('code',$cot->code)->first())
                                                {{$vs->where('code',$cot->code)->first()->value}}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-1" wire:click="conditionsEnc()">
            <div class="card-header" id="headingSix">
                <h5 class="accordion-faq m-0 position-relative">
                    <a class="custom-accordion-title text-white d-block collapsed" data-bs-toggle="collapse" href="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                        {{__('condition.titles')}}
                    </a>
                </h5>
            </div>
            <div id="collapseSix" class="collapse" aria-labelledby="headingSix" data-bs-parent="#custom-accordion-one" style="">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">Id</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">{{__('condition.code')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">{{__('condition.description')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">{{__('condition.clinical_status')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer">{{__('condition.verification_status')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($conditions as $c)
                                <tr>
                                    <td>{{$c->id}}</td>
                                    <td>{{$c->code}}</td>
                                    <td>{{$c->icd10Code->description_es}}</td>
                                    <td>{{$c->clinical_status}}</td>
                                    <td>{{$c->verification_status}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-1" wire:click="medicationRequests()">
            <div class="card-header" id="headingSeven">
                <h5 class="accordion-faq m-0 position-relative">
                    <a class="custom-accordion-title text-white d-block collapsed" data-bs-toggle="collapse" href="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                        {{__('medication.titles')}}
                    </a>
                </h5>
            </div>
            <div id="collapseSeven" class="collapse" aria-labelledby="headingSeven" data-bs-parent="#custom-accordion-one" style="">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('encounter.title')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.medicine')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.instruction')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.status')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.valid_from')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('medication.valid_to')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($medications as $key=>$items)
                                @foreach($items as $row)
                                    <tr class="">
                                        <td>{{$row->encounter_id}}</td>
                                        <td>{{$row->medicine->full_name}}</td>
                                        <td>{{$row->dosage_text}}</td>
                                        <td>{{$row->status}} </td>
                                        <td>{{$row->valid_from}} </td>
                                        <td>{{$row->valid_to}}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mb-1" wire:click="serviceRequests()">
            <div class="card-header" id="heading8">
                <h5 class="accordion-faq m-0 position-relative">
                    <a class="text-white custom-accordion-title text-white d-block collapsed" data-bs-toggle="collapse" href="#collapse8" aria-expanded="false" aria-controls="collapseSeven">
                        {{__('services.titles')}}
                    </a>
                </h5>
            </div>
            <div id="collapse8" class="collapse" aria-labelledby="heading8" data-bs-parent="#custom-accordion-one" style="">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table border-0 custom-table comman-table mb-0">
                            <thead>
                            <tr>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('encounter.title')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.code')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.description')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.service_type')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.status')}}</th>
                                <th class="border-b border-gray-300 p-2 cursor-pointer" >{{__('services.occurrence_start')}}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($services as $key=>$items)
                                @foreach($items as $c)
                                    <tr>
                                        <td>{{$c->encounter_id}}</td>
                                        <td>{{$c->code}}</td>
                                        <td>{{$c->cpt->description_es}}</td>
                                        <td>{{$c->service_type}}</td>
                                        <td>{{$c->status}}</td>
                                        <td>{{$c->occurrence_start}}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
