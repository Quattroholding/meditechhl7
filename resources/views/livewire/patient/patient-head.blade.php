<div class="card">
    <!-- Header del Paciente -->
    <div class="data-header">
        <div class="data-info">
            <div class="data-avatar">
                <div class="profile-user-img">
                    @if($data->avatar())
                        <img src="{{url('storage/'.$data->avatar()->path)}}" style="border-radius: 50px">
                    @else
                        {{ strtoupper(substr($data->name, 0, 1) . substr($data->family_name, 0, 1)) }}
                    @endif


                    <div class="form-group doctor-up-files profile-edit-icon mb-0">
                        <div class="uplod d-flex">
                            <label class="file-upload profile-upbtn mb-0">
                                <input type="file" wire:model="avatar">
                                @error('avatar') <span class="error">{{ $message }}</span> @enderror
                                <img src="{{ URL::asset('/assets/img/icons/camera-icon.svg') }}" alt="Profile">
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="data-details">
                <h1>{{ $data->name }}</h1>
                <div class="data-meta">
                    <span>📅 {{ $data->birth_date ? Carbon\Carbon::parse($data->birth_date)->age . ' años' : 'N/A' }}</span>
                    <span>🆔 {{$data->identifier_type}}:{{ $data->identifier ?? 'N/A' }}</span>
                    <span>📧 {{ $data->email ?? 'N/A' }}</span>
                    <span>📞 {{ $data->phone ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="data-actions">

                @if(auth()->user()->hasRole('doctor') && $typeNote=='medical')
                <button wire:click="openModalNote({{ $data->id }},'medical')" class="btn-head btn-head-light"> 📄  Agregar Nota Médica</button>
                @elseif(auth()->user()->hasRole('doctor') && $typeNote=='private')
                <button wire:click="openModalNote({{ $data->id }},'private')" class="btn-head btn-head-light"> <i class="fa fa-unlock"></i>  {{__('patient.add_note_private')}}</button>
                @endif
                @if(auth()->user()->hasRole('doctor') or auth()->user()->hasRole('paciente'))
                <livewire:patient.add-medical-history :patient_id="$data->id"/>
                @endif
                <livewire:patient.add-insurance :patient_id="$data->id"/>
                <livewire:modal-add-notes :patient_id="$data->id"/>
                {{--}}
                <button wire:click="exportToPDF" class="btn btn-light">
                    📄 Exportar PDF
                </button>
                <button wire:click="exportToFHIR" class="btn btn-light">
                    🔗 Exportar FHIR
                </button>
                {{--}}
            </div>
        </div>
    </div>
</div>
