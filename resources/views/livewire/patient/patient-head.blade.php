<div class="card">
    <style>
        .data-header {
            /*background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);*/
            background: #003b62;
            color: white;
            border-radius: 20px;
            padding: 30px;
            /*margin-bottom: 30px;
            box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);*/
            position: relative;
            overflow: hidden;
        }

        .data-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }

        .data-info {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 30px;
            align-items: center;
            position: relative;
        }

        .data-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
            border: 4px solid rgba(255, 255, 255, 0.3);
        }

        .data-details h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .data-meta {
            font-size: 16px;
            opacity: 0.9;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .data-actions {
            display: flex;
            gap: 12px;
        }

        .btn-head {
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-head-light {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
    </style>
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
                <livewire:patient.add-medical-history :patient_id="$data->id"/>
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
