<div class="card">
    <style>

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
    <!-- Header del doctor -->
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
                    <span>🆔 {{$data->identifier_type}}:{{ $data->identifier ?? 'N/A' }}</span>
                    <span>📧 {{ $data->email ?? 'N/A' }}</span>
                    <span>📞 {{ $data->phone ?? 'N/A' }}</span>
                    <span>📅 Citas Agendadas : {{$data->appointments()->count()}}</span>
                    <span>🏥  Consultas Finalizadas : {{$data->encounters()->whereStatus('fulfilled')->count()}}</span>


                </div>
            </div>
            <div class="data-actions">
                <livewire:practitioner.add-qualification :practitioner_id="$data->id"/>
            </div>
        </div>
    </div>
</div>
