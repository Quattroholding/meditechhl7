<div class="col-lg-12">
    <div class="doctor-personals-grp">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title mb-4">{{__('patient.clinical_history')}}</h4>
                <ul class="nav nav-pills navtab-bg nav-justified" role="tablist">
                    @foreach($tabs2 as $tab)
                        @isset($tab['title'])
                        <li class="nav-item" role="presentation">
                            <a href="#{{$tab['title']}}"   wire:click="changeActiveTab('{{$tab['title']}}')"
                               data-bs-toggle="tab"
                               aria-expanded="false"
                               class="nav-link {{$tab['active']}}"
                               aria-selected="false"
                               tabindex="-1"
                               role="tab">
                                {{__('patient.'.$tab['title'])}}
                                <span class="badge bg-danger ms-1">{{$tab['count']}}</span>
                            </a>
                        </li>
                        @endisset
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($tabs2 as $tab)
                        @isset($tab['title'])
                        <div class="tab-pane {{$tab['active']}}" id="{{$tab['title']}}" role="tabpanel">
                            @if($activeTab === $tab['title'])
                                <div wire:init>
                                    @livewire($tab['component'], ['patient_id' => $patient_id], key($tab['title']))
                                </div>
                            @endif
                        </div>
                        @endisset
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
