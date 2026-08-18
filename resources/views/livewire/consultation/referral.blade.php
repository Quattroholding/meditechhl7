<div>
    @if(count($selectedLists) > 0)
        <div class="mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="mb-0" style="color: var(--primary-color); font-weight: 600;">
                    <i class="fas fa-share-square me-2"></i>
                    {{ __('consultation.referral_section.selected_referrals') }}
                </h5>
                <span class="badge" style="background: var(--sami-green); font-size: 0.9rem; padding: 0.4rem 0.8rem;">
                    {{ count($selectedLists) }} {{ __('consultation.referral_section.items') }}
                </span>
            </div>

            <div class="service-cards-container">
                @foreach($selectedLists as $s)
                    <div class="service-card" x-data="{ confirmDelete: false }">
                        <!-- Header de la tarjeta -->
                        <div class="service-card-header">
                            <div class="service-card-title">
                                <i class="fas fa-user-md me-2" style="color: var(--sami-green);"></i>
                                {{ $s->speciality->name }}
                            </div>
                            <button
                                type="button"
                                class="btn-delete-service"
                                @click="confirmDelete = !confirmDelete"
                                title="{{ __('consultation.referral_section.delete') }}"
                            >
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>

                        <!-- Confirmación de borrado -->
                        <div x-show="confirmDelete"
                             x-transition
                             class="delete-confirmation"
                             style="display: none;">
                            <div class="d-flex align-items-center justify-content-between">
                                <span style="color: #721c24; font-weight: 500;">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    {{ __('consultation.referral_section.confirm_delete') }}
                                </span>
                                <div class="d-flex gap-2">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-danger"
                                        @click.prevent="$wire.delete({{$s->id}}); confirmDelete = false;"
                                    >
                                        <i class="fas fa-check me-1"></i>
                                        {{ __('generic.yes') }}
                                    </button>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-secondary"
                                        @click.prevent="confirmDelete = false"
                                    >
                                        <i class="fas fa-times me-1"></i>
                                        {{ __('generic.no') }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Contenido de la tarjeta -->
                        <div class="service-card-body">
                            <!-- Nota de Referencia -->
                            <div class="input-block local-forms">
                                <x-input-label for="referral_note_{{$s->id}}" :value="__('consultation.referral_section.referral_note')" />
                                <x-autosave-input
                                    type="textarea"
                                    :value="$selectedReason[$s->id]"
                                    class="form-control mt-1 block w-full"
                                    rows="2"
                                    wire:model.live.debounce.500ms="selectedReason.{{$s->id}}"
                                    save-method="setReason"
                                    save-key="referral_{{ $s->id }}"
                                />
                            </div>

                            <!-- Selector de Especialista -->
                            <div class="multivalue-item-sustento-container">
                                <div class="input-block local-forms">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <x-input-label for="specialist_{{$s->id}}" :value="__('consultation.referral_section.specialist')" />
                                    </div>

                                    @if($useExternalSpecialist[$s->id] ?? false)
                                        <!-- Formulario de Especialista Externo -->
                                        <div class="specialist-card external">
                                            <div style="margin-bottom: 12px;">
                                                <i class="fa fa-external-link-alt" style="color: #ffc107;"></i>
                                                <strong style="color: #856404; margin-left: 8px;">{{ __('consultation.referral_section.external_specialist') }}</strong>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6 input-block local-forms">
                                                    <x-input-label for="full_name" :value="__('patient.full_name')" required="true"/>
                                                    <x-text-input wire:model.live.debounce.500ms="externalSpecialistName.{{$s->id}}" class="block mt-1 w-full" type="text" placeholder="Dr. Juan Pérez"/>
                                                </div>
                                                <div class="col-md-6 mb-2 input-block local-forms">
                                                    <label class="form-label" style="font-size: 12px; font-weight: 600;">{{ __('consultation.referral_section.phone') }}</label>
                                                    <input type="text" class="form-control form-control-sm" wire:model.live.debounce.500ms="externalSpecialistPhone.{{$s->id}}" placeholder="+507 6000-0000">
                                                </div>
                                                <div class="col-md-6 mb-2 input-block local-forms">
                                                    <label class="form-label" style="font-size: 12px; font-weight: 600;">{{ __('consultation.referral_section.clinic') }}</label>
                                                    <input type="text" class="form-control form-control-sm" wire:model.live.debounce.500ms="externalSpecialistClinic.{{$s->id}}" placeholder="Hospital San Fernando">
                                                </div>
                                                <div class="col-md-6 mb-2 input-block local-forms">
                                                    <button type="button" wire:click="toggleExternalSpecialist({{$s->id}})" class="btn btn-sm btn-warning" style="white-space: nowrap;">
                                                        <i class="fa fa-users"></i>
                                                        {{ __('consultation.referral_section.use_directory') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <!-- Directorio Médico -->
                                        @if($s->referred_to_id && $s->referredTo)
                                            @php
                                                $selectedPractitioner = $s->referredTo;
                                            @endphp

                                            @if($selectedPractitioner)
                                                <!-- Médico seleccionado -->
                                                <div class="specialist-card selected">
                                                    <div style="display: flex; gap: 15px; align-items: center;">
                                                        <div style="flex: 1;">
                                                            <div style="font-weight: 600; color: #155724; margin-bottom: 4px;">
                                                                <i class="fa fa-user-md" style="color: #28a745; margin-right: 5px;"></i>
                                                                {{ $selectedPractitioner->name }}
                                                            </div>
                                                            <div style="font-size: 12px; color: #666;">
                                                                ID: {{ $selectedPractitioner->identifier }}
                                                            </div>
                                                        </div>
                                                        <button type="button" wire:click="openMedicalDirectory({{$s->code}}, {{$s->id}})" class="btn btn-sm btn-outline-primary" style="white-space: nowrap;">
                                                            <i class="fa fa-exchange-alt"></i> {{ __('consultation.referral_section.change') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            <!-- No hay médico seleccionado -->
                                            <div class="specialist-card">
                                                <div style="text-align: center; padding: 20px;">
                                                    <i class="fa fa-user-md" style="font-size: 32px; color: #ccc; margin-bottom: 10px;"></i>
                                                    <div style="color: #666; margin-bottom: 15px; font-size: 14px;">{{ __('consultation.referral_section.no_specialist_selected') }}</div>
                                                    <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                                                        <button type="button" wire:click="openMedicalDirectory({{$s->code}}, {{$s->id}})" class="btn btn-primary btn-sm">
                                                            <i class="fa fa-users"></i> {{ __('consultation.referral_section.view_medical_directory') }}
                                                        </button>
                                                        <button type="button" wire:click="toggleExternalSpecialist({{$s->id}})" class="btn btn-sm btn-info" style="white-space: nowrap;">
                                                            <i class="fa fa-user-edit"></i>
                                                            {{ __('consultation.referral_section.external_specialist_toggle') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    @include('partials.input_saving',['saving'=>$savingEspecialist[$s->id] ?? false, 'saved'=>$savedEspecialist[$s->id] ?? false])
                                    @if(isset($savedEspecialist[$s->id]) && $savedEspecialist[$s->id] && isset($savedEspecialistTimer[$s->id]) && $savedEspecialistTimer[$s->id])
                                        <div wire:poll.3s="resetSavedEspecialist({{$s->id}})" style="display:none;"></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="my-3"></div>

    <div class="selector-field selector-field-on">
        <x-autosave-action save-key="speciality-search" />

        <!-- Área de búsqueda mejorada -->
        <div class="search-area">
            <div class="search-container">
                <button type="button" class="search-icon-btn">
                    <i class="fas fa-search"></i>
                </button>
                <input
                    type="text"
                    wire:model.live="query"
                    class="search-input"
                    placeholder="{{ __('consultation.referral_section.search_specialty') }}"
                >
            </div>
        </div>

        <!-- Resultados de búsqueda -->
        @if(!empty($results))
            <div style="position: absolute; z-index: 1000; width: 100%; background: white; border: 1px solid #ddd; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <!-- Header FIJO -->
                <div style="background: #ffffff; padding: 12px 16px; border-bottom: 2px solid #0d6efd;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div style="font-size: 0.9rem;">
                            <i class="fas fa-search text-primary"></i>
                            <strong>{{ __('consultation.referral_section.medical_specialties') }}</strong>
                        </div>
                        <div class="text-muted" style="font-size: 0.85rem;">
                            <i class="fas fa-list"></i>
                            {{ count($results) }} / {{ $totalResults }}
                            @if($hasMoreResults)
                                <span class="badge bg-primary ms-1">+{{ $totalResults - count($results) }}</span>
                            @endif
                        </div>
                    </div>

                    @if($hasMoreResults)
                        <div class="mt-2">
                            <button type="button" class="btn btn-primary w-100" wire:click="loadMore" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="loadMore">
                                    <i class="fas fa-plus-circle"></i>
                                    {{ __('consultation.referral_section.load_more') }} {{ $totalResults - count($results) }} {{ __('consultation.referral_section.more_results') }}
                                </span>
                                <span wire:loading wire:target="loadMore">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                    {{ __('consultation.referral_section.loading') }}
                                </span>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Contenedor con scroll -->
                <div style="max-height: 320px; min-height: 300px; overflow-y: auto;">
                    @foreach($results as $result)
                        <div class="sel-list-item" wire:click.debounce.300ms="selectOption({{ json_encode($result) }})" x-on:click="window.dispatchEvent(new CustomEvent('autosave-start', { detail: 'speciality-search' }))" style="padding: 10px 16px; cursor: pointer; border-bottom: 1px solid #e9ecef; transition: background 0.15s; display: flex; align-items: center; gap: 10px;" onmouseover="this.style.background='#f0f7ff'" onmouseout="this.style.background='white'">
                            <i class="fas fa-user-md text-primary" style="font-size: 1rem;"></i>
                            <span style="font-size: 0.9rem; color: #212529; flex: 1; font-weight: 500;">{{ $result['name'] }}</span>
                        </div>
                    @endforeach

                    @if($hasMoreResults)
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); width: 100%; padding: 12px; text-align: center; cursor: pointer; border: none; color: white; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.2);" wire:click.prevent="loadMore" onmouseover="this.style.transform='scale(1.02)'; this.style.boxShadow='0 6px 12px rgba(0,0,0,0.3)'" onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 6px rgba(0,0,0,0.2)'">
                            <div wire:loading.remove wire:target="loadMore">
                                <i class="fas fa-chevron-down" style="font-size: 1.2rem;"></i>
                                <div style="font-size: 1rem; margin-top: 4px;">
                                    <strong>{{ __('consultation.referral_section.load_more_results') }}</strong>
                                </div>
                                <div style="font-size: 0.9rem; margin-top: 4px; opacity: 0.9;">
                                    {{ __('consultation.referral_section.results_available_prefix') }} {{ $totalResults - count($results) }} {{ __('consultation.referral_section.results_available') }}
                                </div>
                            </div>
                            <div wire:loading wire:target="loadMore">
                                <div class="spinner-border text-white" role="status">
                                    <span class="visually-hidden">{{ __('consultation.referral_section.loading') }}</span>
                                </div>
                                <div style="margin-top: 8px;">{{ __('consultation.referral_section.loading_results') }}</div>
                            </div>
                        </div>
                    @endif

                    @if(!$hasMoreResults && count($results) > 0)
                        <div style="padding: 10px 12px; text-align: center; color: #6c757d; font-size: 0.85rem; background: #f8f9fa;">
                            <i class="fas fa-check-circle text-success"></i>
                            {{ __('consultation.referral_section.all_results_shown') }}
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <div style="height:200px;">&nbsp;</div>

    <!-- Modal de Directorio Médico -->
    @if($showMedicalDirectory)
        <div class="modal-overlay" wire:click="closeMedicalDirectory" style="z-index: 10000;">
            <div class="modal-content" wire:click.stop style="max-width: 1200px; max-height: 90vh; overflow-y: auto;">
                <div class="modal-header">
                    <h2 class="modal-title">{{ __('consultation.referral_section.medical_directory') }} - {{ $currentSpecialityName }}</h2>
                    <button wire:click="closeMedicalDirectory" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
                </div>

                <div style="padding: 20px;">
                    <!-- Buscador -->
                    <div style="margin-bottom: 20px;">
                        <input type="text" wire:model.live.debounce.300ms="searchPractitioner" class="form-control" placeholder="{{ __('consultation.referral_section.search_by_name_or_id') }}">
                    </div>

                    <!-- Loading state -->
                    <div wire:loading.delay wire:target="searchPractitioner,loadPractitioners" style="text-align: center; padding: 20px;">
                        <div style="display: inline-flex; align-items: center; gap: 10px;">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            <span>{{ __('consultation.referral_section.loading') }}</span>
                        </div>
                    </div>

                    <!-- Grid de médicos -->
                    @if(count($practitioners) > 0)
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
                            @foreach($practitioners as $practitioner)
                                <div style="background: white; border: 1px solid #e0e0e0; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: box-shadow 0.2s;">
                                    <!-- Header de la tarjeta -->
                                    <div style="padding: 20px;">
                                        <div style="display: flex; align-items: start; gap: 15px;">
                                            <!-- Avatar -->
                                            <div>
                                                @if($practitioner->avatar())
                                                    <img width="60" height="60" src="{{url('storage/'.$practitioner->avatar()->path)}}" style="border-radius: 50%; object-fit: cover;" alt="">
                                                @else
                                                    <img width="60" height="60" src="{{url('assets/img/profiles/avatar-02.jpg')}}" style="border-radius: 50%; object-fit: cover;" alt="">
                                                @endif
                                            </div>

                                            <!-- Información -->
                                            <div style="flex: 1; min-width: 0;">
                                                <h3 style="margin: 0 0 5px 0; font-size: 16px; font-weight: 600; color: #333;">
                                                    {{ $practitioner->name }}
                                                </h3>
                                                <p style="margin: 0 0 8px 0; font-size: 13px; color: #666;">
                                                    {{ __('consultation.referral_section.id') }}: {{ $practitioner->identifier }}
                                                </p>
                                                <span style="display: inline-flex; align-items: center; padding: 4px 10px; background: #d4edda; color: #155724; border-radius: 12px; font-size: 11px; font-weight: 500;">
                                                    <span style="width: 6px; height: 6px; background: #28a745; border-radius: 50%; margin-right: 5px;"></span>
                                                    {{ __('consultation.referral_section.active') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Información de contacto -->
                                    @if($practitioner->phone || $practitioner->email)
                                        <div style="padding: 0 20px 15px 20px; border-top: 1px solid #f0f0f0;">
                                            <div style="margin-top: 15px;">
                                                @if($practitioner->phone)
                                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px; font-size: 13px; color: #666;">
                                                        <i class="fa fa-phone" style="width: 16px;"></i>
                                                        <span>{{ $practitioner->phone }}</span>
                                                    </div>
                                                @endif
                                                @if($practitioner->email)
                                                    <div style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #666;">
                                                        <i class="fa fa-envelope" style="width: 16px;"></i>
                                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $practitioner->email }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Horario Laboral -->
                                    @if($practitioner->user && $practitioner->user->workingHours->count() > 0)
                                        <div style="padding: 15px 20px; border-top: 1px solid #f0f0f0;">
                                            <h4 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600; color: #333;">{{ __('consultation.referral_section.working_hours') }}</h4>
                                            <div style="display: flex; flex-direction: column; gap: 6px;">
                                                @foreach($practitioner->user->workingHours()->limit(3)->get() as $wh)
                                                    <div style="font-size: 12px; color: #666;">
                                                        <strong>{{ $wh->day_of_week }}:</strong> {{ substr($wh->start_time,0,5) }} - {{ substr($wh->end_time,0,5) }}
                                                    </div>
                                                @endforeach
                                                @if($practitioner->user->workingHours->count() > 3)
                                                    <span style="font-size: 11px; color: #999;">+{{ $practitioner->user->workingHours->count() - 3 }} {{ __('consultation.referral_section.more_days') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Especialidades -->
                                    @if($practitioner->qualifications->count() > 0)
                                        <div style="padding: 15px 20px; border-top: 1px solid #f0f0f0;">
                                            <h4 style="margin: 0 0 10px 0; font-size: 13px; font-weight: 600; color: #333;">{{ __('consultation.referral_section.specialties') }}</h4>
                                            <div style="display: flex; flex-direction: column; gap: 8px;">
                                                @foreach($practitioner->qualifications->take(2) as $qualification)
                                                    <div>
                                                        <p style="margin: 0; font-size: 12px; font-weight: 500; color: #333;">
                                                            {{ $qualification->medicalSpeciality->name }}
                                                        </p>
                                                        @if($qualification->issuer_name)
                                                            <p style="margin: 2px 0 0 0; font-size: 11px; color: #999;">
                                                                {{ $qualification->issuer_name }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                @if($practitioner->qualifications->count() > 2)
                                                    <span style="font-size: 11px; color: #999;">+{{ $practitioner->qualifications->count() - 2 }} {{ __('consultation.referral_section.more_specialties') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Botón de selección -->
                                    <div style="padding: 15px 20px; background: #f8f9fa; border-top: 1px solid #e0e0e0;">
                                        <button wire:click="selectPractitioner({{ $practitioner->id }})" class="btn btn-primary" style="width: 100%;">
                                            <i class="fa fa-check"></i> {{ __('consultation.referral_section.select_doctor') }}
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px 20px;">
                            <i class="fa fa-user-md" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                            <h3 style="margin: 0 0 10px 0; font-size: 16px; color: #666;">{{ __('consultation.referral_section.no_professionals_found') }}</h3>
                            <p style="margin: 0; font-size: 14px; color: #999;">
                                @if($searchPractitioner)
                                    {{ __('consultation.referral_section.adjust_search') }}
                                @else
                                    {{ __('consultation.referral_section.no_professionals_specialty') }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Search Area */
        .search-area {
            display: flex;
            gap: 1rem;
            align-items: center;
            padding: 1.25rem;
            background: white;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .search-container {
            flex: 1;
            display: flex;
            align-items: stretch;
            height: 48px;
        }

        .search-icon-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, #003366 100%);
            color: white;
            border: 2px solid var(--primary-color);
            border-right: none;
            border-radius: 8px 0 0 8px;
            padding: 0 1.25rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 56px;
            flex-shrink: 0;
        }

        .search-icon-btn:hover {
            background: linear-gradient(135deg, #003366 0%, #002244 100%);
            border-color: #003366;
        }

        .search-input {
            flex: 1;
            border: 2px solid #dee2e6;
            border-left: none;
            border-radius: 0 8px 8px 0;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .search-input:focus {
            border-color: var(--sami-green);
            box-shadow: 0 0 0 3px rgba(122, 193, 66, 0.1);
        }

        .search-container:focus-within .search-icon-btn {
            background: linear-gradient(135deg, var(--sami-green) 0%, #63a733 100%);
            border-color: var(--sami-green);
        }

        /* Service Cards Container */
        .service-cards-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        /* Service Card */
        .service-card {
            background: white;
            border-radius: 12px;
            border: 2px solid #e9ecef;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .service-card:hover {
            border-color: var(--sami-green);
            box-shadow: 0 4px 12px rgba(122, 193, 66, 0.15);
            transform: translateY(-2px);
        }

        /* Service Card Header */
        .service-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #dee2e6;
        }

        .service-card-title {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.05rem;
            flex: 1;
        }

        .btn-delete-service {
            background: white;
            border: 2px solid #dc3545;
            color: #dc3545;
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 0.9rem;
        }

        .btn-delete-service:hover {
            background: #dc3545;
            color: white;
            transform: scale(1.05);
        }

        /* Delete Confirmation */
        .delete-confirmation {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 0.75rem 1.25rem;
            margin: 0;
        }

        /* Service Card Body */
        .service-card-body {
            padding: 1.25rem;
        }

        /* Specialist Cards */
        .specialist-card {
            background: #ffffff;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .specialist-card.external {
            border-color: #ffc107;
            background: linear-gradient(135deg, #fffbf0 0%, #fff8e1 100%);
        }

        .specialist-card.selected {
            border-color: #28a745;
            background: linear-gradient(135deg, #f0fff4 0%, #e6f9ed 100%);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .search-area {
                flex-direction: column;
                gap: 0.75rem;
            }

            .service-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }

            .btn-delete-service {
                width: 100%;
                justify-content: center;
            }

            .service-card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .search-area {
                padding: 0.75rem;
            }

            .search-container {
                height: 42px;
            }

            .search-icon-btn {
                padding: 0 1rem;
                font-size: 1rem;
                min-width: 48px;
            }

            .search-input {
                padding: 0.6rem 0.75rem;
                font-size: 0.9rem;
            }
        }
    </style>
</div>
