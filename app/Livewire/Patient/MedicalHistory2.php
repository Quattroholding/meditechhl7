<?php

namespace App\Livewire\Patient;

use App\Models\ClinicalImpression;
use App\Models\Condition;
use App\Models\Encounter;
use App\Models\MedicalLeave;
use App\Models\MedicationRequest;
use App\Models\Note;
use App\Models\Patient;
use App\Models\PatientPractitionerAuthorization;
use App\Models\PhysicalExam;
use App\Models\PresentIllness;
use App\Models\Scopes\EncouterScope;
use App\Models\ServiceRequest;
use App\Models\VitalSign;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class MedicalHistory2 extends Component
{
    use WithPagination;

    // Propiedades principales
    public $patient;

    public $patientId;

    public $activeSection = 'overview';

    public $selectedTimeRange = 'all';

    public $searchTerm = '';

    public $showFilters = false;

    public $groupByEncounter = true;

    public $groupPhysicalExamsByEncounter = true;

    public $groupVitalSignsByEncounter = true;

    // Control de secciones
    public $sectionsLoaded = [];

    public $isLoading = false;

    // Filtros
    public $dateFrom = '';

    public $dateTo = '';

    public $selectedProvider = '';

    public $selectedCondition = '';

    // Datos cargados
    public $overviewData = [];

    public $encounters = [];

    public $vitalSigns = [];

    public $physicalExams = [];

    public $conditions = [];

    public $presentIllnesses = [];

    public $medicalRequests = [];

    public $serviceRequests = [];

    public $medicalHistories = [];

    public $medicalNotes = [];

    public $personalNotes = [];

    public $medicalLeaves = [];

    // Configuración de paginación
    public $perPage = 10;

    public $encountersPerPage = 5;

    public $currentEncounterPage = 1;

    public $totalEncounters = 0;

    public $currentPhysicalExamPage = 1;

    public $totalPhysicalExamEncounters = 0;

    public $currentVitalSignPage = 1;

    public $totalVitalSignEncounters = 0;

    public $currentEncountersPage = 1;

    public $totalEncountersCount = 0;

    public $currentConditionsPage = 1;

    public $totalConditionsCount = 0;

    public $currentPresentIllnessPage = 1;

    public $totalPresentIllnessCount = 0;

    public $currentMedicalLeavesPage = 1;

    public $totalMedicalLeavesCount = 0;

    // Filtros de búsqueda por sección
    public $conditionsSearchTerm = '';

    public $completeAutorization = false;

    // Filtro de tiempo para gráficas de signos vitales (máximo 5 años)
    public $vitalSignsChartPeriod = 'last_5_years';

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'activeSection' => ['except' => 'overview'],
        'selectedTimeRange' => ['except' => 'all'],
        'searchTerm' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function mount($patientId)
    {
        $this->patientId = $patientId;
        $this->patient = Patient::findOrFail($patientId);
        if (auth()->user()->hasRole('doctor')) {
            $this->completeAutorization = PatientPractitionerAuthorization::wherePatientId($this->patientId)
                ->wherePractitionerId(auth()->user()->practitioner->id)->first();
        }
        $this->loadOverviewData();
    }

    public function render()
    {
        if (request()->has('activeSection')) {
            $this->activeSection = request()->get('activeSection');
            $this->loadSection($this->activeSection);
        }

        return view('livewire.patient.medical-history2', [
            'sectionData' => $this->getSectionData(),
            'timelineData' => $this->getTimelineData(),
        ]);
    }
    // ===========================================
    // MÉTODOS DE CARGA DE DATOS
    // ===========================================

    public function loadOverviewData()
    {
        $this->isLoading = true;
        $current_medications = [];
        $total_personal_notes = 0;
        $totalNotes = 0;

        if (auth()->user()->hasRole('doctor')) {
            $totalNotes = ClinicalImpression::where('patient_id', $this->patientId)->wherePractitionerId(auth()->user()->practitioner->id)->count();
        }
        if (auth()->user()->hasRole('recepcionista')) {
            $totalNotes = ClinicalImpression::where('patient_id', $this->patientId)->count();
        }

        if (Encounter::where('patient_id', $this->patientId)->count() > 0) {
            $current_medications = $this->patient->current_medications;
        }

        if (auth()->user()->hasRole('doctor')) {
            $total_personal_notes = Note::wherePractitionerId(auth()->user()->practitioner->id)->where('patient_id', $this->patientId)->count();
        }

        // Cargar historiales médicos por categoría - convertir a arrays
        $medicalHistories = \App\Models\MedicalHistory::where('patient_id', $this->patientId)
            ->where('category', '<>', 'allergy')
            ->orderBy('recorded_date', 'desc')
            ->get()
            ->groupBy('category')
            ->map(function ($histories) {
                return $histories->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'title' => $history->title,
                        'description' => $history->description,
                        'recorded_date' => $history->recorded_date,
                        'category' => $history->category,
                        'clinical_status' => $history->clinical_status,
                    ];
                })->toArray();
            })->toArray();

        // Convertir alergias a array
        $allergies = $this->patient->allergies()->get()->map(function ($allergy) {
            return [
                'id' => $allergy->id,
                'title' => $allergy->title,
                'description' => $allergy->description,
                'category' => $allergy->category,
            ];
        })->toArray();

        // Convertir medicamentos a array
        $medications = collect($current_medications)->map(function ($medication) {
            return [
                'id' => $medication->id ?? null,
                'medicine_name' => $medication->medication2?->display ?? $medication->medicine?->full_name ?? 'N/A',
                'dosage_text' => $medication->dosage_text ?? '',
            ];
        })->toArray();

        // Resumen general del paciente
        // Aplicar filtros según rol
        $isDoctor = auth()->user()->hasRole('doctor') && ! $this->completeAutorization;
        $practitionerId = $isDoctor ? auth()->user()->practitioner->id : null;

        // Total encounters
        $encountersQuery = Encounter::where('patient_id', $this->patientId)->when($this->completeAutorization, function ($q) {
            $q->withoutGlobalScope(EncouterScope::class);
        });
        if ($isDoctor) {
            $encountersQuery->where('practitioner_id', $practitionerId);
        }
        $totalEncounters = $encountersQuery->count();
        $lastVisit = $encountersQuery->latest('created_at')->first()?->created_at;

        // Active conditions
        $activeConditions = Condition::where('patient_id', $this->patientId)->where('clinical_status', 'active')->count();

        // Total requests (medicamentos + servicios)
        $medicationRequestsQuery = MedicationRequest::where('patient_id', $this->patientId);
        if ($isDoctor) {
            $medicationRequestsQuery->whereHas('encounter', function ($q) use ($practitionerId) {
                $q->where('practitioner_id', $practitionerId);
            });
        }

        $serviceRequestsQuery = ServiceRequest::where('patient_id', $this->patientId);
        if ($isDoctor) {
            $serviceRequestsQuery->whereHas('encounter', function ($q) use ($practitionerId) {
                $q->where('practitioner_id', $practitionerId);
            });
        }
        $totalRequests = $medicationRequestsQuery->count() + $serviceRequestsQuery->count();

        // Vital signs count
        $vitalSignsQuery = VitalSign::where('patient_id', $this->patientId);
        if ($isDoctor) {
            $vitalSignsQuery->whereHas('encounter', function ($q) use ($practitionerId) {
                $q->where('practitioner_id', $practitionerId);
            });
        }
        $vitalSignsCount = $vitalSignsQuery->count();

        // Medical leaves count
        $medicalLeavesQuery = MedicalLeave::where('patient_id', $this->patientId);
        if ($isDoctor) {
            $medicalLeavesQuery->where('practitioner_id', $practitionerId);
        }
        $totalMedicalLeaves = $medicalLeavesQuery->count();

        $this->overviewData = [
            'total_encounters' => $totalEncounters,
            'active_conditions' => $activeConditions,
            'last_visit' => $lastVisit,
            'total_requests' => $totalRequests,
            'vital_signs_count' => $vitalSignsCount,
            'total_medical_leaves' => $totalMedicalLeaves,
            'allergies' => $allergies,
            'medications' => $medications,
            'total_notes' => $totalNotes,
            'total_personal_notes' => $total_personal_notes,
            'recent_activity' => $this->getRecentActivity(),
            'medical_histories' => $medicalHistories,
        ];

        $this->sectionsLoaded['overview'] = true;
        $this->isLoading = false;
    }

    public function loadSection($section)
    {
        $this->isLoading = true;
        $this->activeSection = $section;

        match ($section) {
            'overview' => $this->loadOverviewData(),
            'encounters' => $this->loadEncounters(),
            'vital-signs' => $this->loadVitalSigns(),
            'physical-exams' => $this->loadPhysicalExams(),
            'conditions' => $this->loadConditions(),
            'present-illnesses' => $this->loadPresentIllnesses(),
            'medical-requests' => $this->loadMedicalRequests(),
            'service-requests' => $this->loadServiceRequests(),
            'medical-histories' => $this->loadMedicalHistories(),
            'medical-notes' => $this->loadMedicalNotes(),
            'personal-notes' => $this->loadPersonalNotes(),
            'medical-leaves' => $this->loadMedicalLeaves(),
            default => null
        };

        if (! in_array($section, $this->sectionsLoaded)) {
            $this->sectionsLoaded[] = $section;
        }
        $this->isLoading = false;
    }

    private function loadEncounters()
    {

        $query = Encounter::when($this->completeAutorization, function ($q) {
            $q->withoutGlobalScope(EncouterScope::class);
        })->where('patient_id', $this->patientId)
            ->with(['practitioner', 'medicalSpeciality', 'diagnoses.condition.icd10Code'])
            ->orderBy('start', 'desc');

        $allEncounters = $this->applyFilters($query, 'start')->get();

        // Implementar paginación para encounters
        $this->totalEncountersCount = $allEncounters->count();
        $offset = ($this->currentEncountersPage - 1) * $this->encountersPerPage;
        $paginatedEncounters = $allEncounters->slice($offset, $this->encountersPerPage);

        $this->encounters = [
            'data' => $paginatedEncounters,
            'total' => $this->totalEncountersCount,
            'current_page' => $this->currentEncountersPage,
            'per_page' => $this->encountersPerPage,
            'last_page' => ceil($this->totalEncountersCount / $this->encountersPerPage),
            'from' => $this->totalEncountersCount > 0 ? $offset + 1 : 0,
            'to' => min($offset + $this->encountersPerPage, $this->totalEncountersCount),
        ];

    }

    private function loadVitalSigns()
    {
        $query = VitalSign::where('patient_id', $this->patientId)
            ->with(['encounter.practitioner', 'encounter.medicalSpeciality', 'practitioner', 'observationType'])
            ->orderBy('effective_date', 'desc');

        // Si es doctor, filtrar solo signos vitales de sus encounters
        if (auth()->user()->hasRole('doctor') && ! $this->completeAutorization) {
            $practitionerId = auth()->user()->practitioner->id;
            $query->whereHas('encounter', function ($q) use ($practitionerId) {
                $q->where('practitioner_id', $practitionerId);
            });
        }

        $vitalSigns = $this->applyFilters($query, 'effective_date')->get();

        if ($this->groupVitalSignsByEncounter) {
            // Agrupar por encounter
            $groupedVitals = $vitalSigns->groupBy('encounter_id');

            // Crear estructura agrupada por encounter
            $encounterGroups = [];

            foreach ($groupedVitals as $encounterId => $vitals) {
                $encounterGroups[$encounterId] = [
                    'encounter' => $vitals->first()->encounter,
                    'vital_signs' => $vitals,
                    'vital_signs_summary' => $this->createVitalSignsSummary($vitals),
                ];
            }

            // Ordenar encounters por fecha más reciente
            $encounterGroups = collect($encounterGroups)->sortByDesc(function ($group) {
                if ($group['encounter']) {
                    return $group['encounter']->start ?? $group['encounter']->created_at;
                }
            });

            // Implementar paginación para encounters agrupados
            $this->totalVitalSignEncounters = $encounterGroups->count();
            $offset = ($this->currentVitalSignPage - 1) * $this->encountersPerPage;
            $paginatedGroups = $encounterGroups->slice($offset, $this->encountersPerPage);

            $this->vitalSigns = [
                'grouped_by_encounter' => $paginatedGroups,
                'total_encounters' => $this->totalVitalSignEncounters,
                'current_page' => $this->currentVitalSignPage,
                'per_page' => $this->encountersPerPage,
                'last_page' => ceil($this->totalVitalSignEncounters / $this->encountersPerPage),
                'from' => $this->totalVitalSignEncounters > 0 ? $offset + 1 : 0,
                'to' => min($offset + $this->encountersPerPage, $this->totalVitalSignEncounters),
                'original_vitals' => $vitalSigns,
            ];
        } else {
            $this->vitalSigns = $vitalSigns;
        }
    }

    private function createVitalSignsSummary($vitals)
    {
        $summary = [
            'blood_pressure' => ['systolic' => null, 'diastolic' => null],
            'heart_rate' => null,
            'temperature' => null,
            'oxygen_saturation' => null,
            'weight' => null,
            'height' => null,
            'bmi' => null,
        ];

        foreach ($vitals as $vital) {
            switch ($vital->code) {
                case '8480-6': // Presión sistólica
                    $summary['blood_pressure']['systolic'] = $vital->value;
                    break;
                case '8462-4': // Presión diastólica
                    $summary['blood_pressure']['diastolic'] = $vital->value;
                    break;
                case '8867-4': // Frecuencia cardíaca
                    $summary['heart_rate'] = $vital->value;
                    break;
                case '8310-5': // Temperatura corporal
                    $summary['temperature'] = $vital->value;
                    break;
                case '2708-6': // Saturación de oxígeno
                    $summary['oxygen_saturation'] = $vital->value;
                    break;
                case '29463-7': // Peso
                    $summary['weight'] = $vital->value;
                    break;
                case '8302-2': // Altura
                    $summary['height'] = $vital->value;
                    break;
                case '39156-5': // BMI
                    $summary['bmi'] = $vital->value;
                    break;
            }
        }

        return $summary;
    }

    private function loadPhysicalExams()
    {
        $query = PhysicalExam::where('patient_id', $this->patientId)
            ->with(['encounter.practitioner', 'encounter.medicalSpeciality', 'practitioner', 'observationType'])
            ->orderBy('created_at', 'desc');

        // Si es doctor, filtrar solo exámenes físicos de sus encounters
        if (auth()->user()->hasRole('doctor')) {
            $practitionerId = auth()->user()->practitioner->id;
            $query->whereHas('encounter', function ($q) use ($practitionerId) {
                $q->where('practitioner_id', $practitionerId);
            });
        }

        $physicalExams = $this->applyFilters($query, 'created_at')->get();

        // Filtrar exámenes que tengan al menos un hallazgo o dato registrado
        $physicalExams = $physicalExams->filter(function ($exam) {
            return $exam->name ||
                   $exam->finding ||
                   $exam->cardiovascular_findings ||
                   $exam->respiratory_findings ||
                   $exam->abdominal_findings ||
                   $exam->neurological_findings ||
                   $exam->musculoskeletal_findings ||
                   $exam->skin_findings ||
                   $exam->impression ||
                   $exam->recommendations;
        });

        if ($this->groupPhysicalExamsByEncounter) {
            // Agrupar por encounter
            $groupedExams = $physicalExams->groupBy('encounter_id');

            // Crear estructura agrupada por encounter
            $encounterGroups = [];

            foreach ($groupedExams as $encounterId => $exams) {
                $encounterGroups[$encounterId] = [
                    'encounter' => $exams->first()->encounter,
                    'physical_exams' => $exams,
                ];
            }

            // Ordenar encounters por fecha más reciente
            $encounterGroups = collect($encounterGroups)->sortByDesc(function ($group) {
                if ($group['encounter']) {
                    return $group['encounter']->start ?? $group['encounter']->created_at;
                }
            });

            // Implementar paginación para encounters agrupados
            $this->totalPhysicalExamEncounters = $encounterGroups->count();
            $offset = ($this->currentPhysicalExamPage - 1) * $this->encountersPerPage;
            $paginatedGroups = $encounterGroups->slice($offset, $this->encountersPerPage);

            $this->physicalExams = [
                'grouped_by_encounter' => $paginatedGroups,
                'total_encounters' => $this->totalPhysicalExamEncounters,
                'current_page' => $this->currentPhysicalExamPage,
                'per_page' => $this->encountersPerPage,
                'last_page' => ceil($this->totalPhysicalExamEncounters / $this->encountersPerPage),
                'from' => $this->totalPhysicalExamEncounters > 0 ? $offset + 1 : 0,
                'to' => min($offset + $this->encountersPerPage, $this->totalPhysicalExamEncounters),
                'original_exams' => $physicalExams,
            ];
        } else {
            $this->physicalExams = $physicalExams;
        }

    }

    private function loadConditions()
    {
        $query = Condition::where('patient_id', $this->patientId)
            ->with(['encounter.practitioner', 'encounter.medicalSpeciality', 'practitioner', 'icd10Code'])
            ->orderBy('onset_date', 'desc');

        // Aplicar filtro de búsqueda por nombre o código ICD-10
        if ($this->conditionsSearchTerm) {
            $query->where(function ($q) {
                $q->where('onset_info', 'like', '%'.$this->conditionsSearchTerm.'%')
                    ->orWhere('code', 'like', '%'.$this->conditionsSearchTerm.'%');
            });
        }

        $allConditions = $this->applyFilters($query, 'onset_date')->get();

        // Implementar paginación para conditions
        $this->totalConditionsCount = $allConditions->count();
        $offset = ($this->currentConditionsPage - 1) * $this->encountersPerPage;
        $paginatedConditions = $allConditions->slice($offset, $this->encountersPerPage);

        $this->conditions = [
            'data' => $paginatedConditions,
            'total' => $this->totalConditionsCount,
            'current_page' => $this->currentConditionsPage,
            'per_page' => $this->encountersPerPage,
            'last_page' => ceil($this->totalConditionsCount / $this->encountersPerPage),
            'from' => $this->totalConditionsCount > 0 ? $offset + 1 : 0,
            'to' => min($offset + $this->encountersPerPage, $this->totalConditionsCount),
        ];
    }

    private function loadPresentIllnesses()
    {
        $query = PresentIllness::where('patient_id', $this->patientId)
            ->with([
                'encounter' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'encounter.practitioner',
                'encounter.medicalSpeciality',
                'practitioner',
            ])
            ->orderBy('created_at', 'desc');

        // Si es doctor, filtrar solo present illnesses de sus encounters
        if (auth()->user()->hasRole('doctor') && ! $this->completeAutorization) {
            $practitionerId = auth()->user()->practitioner->id;
            $query->whereHas('encounter', function ($q) use ($practitionerId) {
                $q->where('practitioner_id', $practitionerId);
            });
        }

        $allPresentIllnesses = $this->applyFilters($query, 'created_at')->get();

        // Agrupar por encounter
        $groupedIllnesses = $allPresentIllnesses->groupBy('encounter_id');

        // Crear estructura agrupada por encounter
        $encounterGroups = [];

        foreach ($groupedIllnesses as $encounterId => $illnesses) {
            $encounterGroups[$encounterId] = [
                'encounter' => $illnesses->first()->encounter,
                'present_illnesses' => $illnesses,
            ];
        }

        // Ordenar encounters por fecha más reciente
        $encounterGroups = collect($encounterGroups)->sortByDesc(function ($group) {
            if ($group['encounter']) {
                return $group['encounter']->start ?? $group['encounter']->created_at;
            }
        });

        // Implementar paginación para encounters agrupados
        $this->totalPresentIllnessCount = $encounterGroups->count();
        $offset = ($this->currentPresentIllnessPage - 1) * $this->encountersPerPage;
        $paginatedGroups = $encounterGroups->slice($offset, $this->encountersPerPage);

        $this->presentIllnesses = [
            'grouped_by_encounter' => $paginatedGroups,
            'total' => $this->totalPresentIllnessCount,
            'current_page' => $this->currentPresentIllnessPage,
            'per_page' => $this->encountersPerPage,
            'last_page' => ceil($this->totalPresentIllnessCount / $this->encountersPerPage),
            'from' => $this->totalPresentIllnessCount > 0 ? $offset + 1 : 0,
            'to' => min($offset + $this->encountersPerPage, $this->totalPresentIllnessCount),
            'original_illnesses' => $allPresentIllnesses,
        ];
    }

    private function loadMedicalRequests()
    {
        // Cargar medicamentos
        $medicationsQuery = MedicationRequest::where('patient_id', $this->patientId)
            ->with([
                'encounter' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'encounter.practitioner',
                'encounter.medicalSpeciality',
                'practitioner',
            ])
            ->orderBy('created_at', 'desc');

        // Si es doctor, filtrar solo medicamentos de sus encounters
        if (auth()->user()->hasRole('doctor') && ! $this->completeAutorization) {
            $practitionerId = auth()->user()->practitioner->id;
            $medicationsQuery->whereHas('encounter', function ($q) use ($practitionerId) {
                $q->where('practitioner_id', $practitionerId);
            });
        }

        $medications = $this->applyFilters($medicationsQuery, 'created_at')->get();

        // Cargar servicios
        $servicesQuery = ServiceRequest::where('patient_id', $this->patientId)
            ->with(['encounter.practitioner', 'encounter.medicalSpeciality', 'practitioner', 'cpt', 'observations'])
            ->orderBy('created_at', 'desc');

        // Si es doctor, filtrar solo servicios de sus encounters
        if (auth()->user()->hasRole('doctor') && ! $this->completeAutorization) {
            $practitionerId = auth()->user()->practitioner->id;
            $servicesQuery->whereHas('encounter', function ($q) use ($practitionerId) {
                $q->where('practitioner_id', $practitionerId);
            });
        }

        $services = $this->applyFilters($servicesQuery, 'created_at')->get();

        // Agrupar por encounter
        $groupedMedications = $medications->groupBy('encounter_id');
        $groupedServices = $services->groupBy('encounter_id');

        // Crear estructura agrupada por encounter
        $encounterGroups = [];

        // Procesar medicamentos agrupados
        foreach ($groupedMedications as $encounterId => $items) {
            if (! isset($encounterGroups[$encounterId])) {
                $encounterGroups[$encounterId] = [
                    'encounter' => $items->first()->encounter,
                    'medications' => collect([]),
                    'services' => collect([]),
                ];
            }
            $encounterGroups[$encounterId]['medications'] = $items;
        }

        // Procesar servicios agrupados
        foreach ($groupedServices as $encounterId => $items) {
            if (! isset($encounterGroups[$encounterId])) {
                $encounterGroups[$encounterId] = [
                    'encounter' => $items->first()->encounter,
                    'medications' => collect([]),
                    'services' => collect([]),
                ];
            }
            $encounterGroups[$encounterId]['services'] = $items;
        }

        // Ordenar encounters por fecha más reciente
        $encounterGroups = collect($encounterGroups)->sortByDesc(function ($group) {
            if ($group['encounter']) {
                return $group['encounter']->start ?? $group['encounter']->created_at;
            }
        });

        // Implementar paginación manual para encounters agrupados
        if ($this->groupByEncounter) {
            $this->totalEncounters = $encounterGroups->count();
            $offset = ($this->currentEncounterPage - 1) * $this->encountersPerPage;
            $paginatedGroups = $encounterGroups->slice($offset, $this->encountersPerPage);

            $this->medicalRequests['grouped_by_encounter'] = $paginatedGroups;
            $this->medicalRequests['total_encounters'] = $this->totalEncounters;
            $this->medicalRequests['current_page'] = $this->currentEncounterPage;
            $this->medicalRequests['per_page'] = $this->encountersPerPage;
            $this->medicalRequests['last_page'] = ceil($this->totalEncounters / $this->encountersPerPage);
            $this->medicalRequests['from'] = $this->totalEncounters > 0 ? $offset + 1 : 0;
            $this->medicalRequests['to'] = min($offset + $this->encountersPerPage, $this->totalEncounters);
        } else {
            $this->medicalRequests['grouped_by_encounter'] = $encounterGroups;
        }

        // Mantener estructura original para compatibilidad
        $this->medicalRequests['types']['medications'] = $medications;
        $this->medicalRequests['types']['services'] = $services;
    }

    private function loadServiceRequests()
    {
        $query = ServiceRequest::where('patient_id', $this->patientId)
            ->with(['encounter', 'practitioner', 'observations'])
            ->orderBy('created_at', 'desc');

        // $this->serviceRequests = $this->applyFilters($query, 'created_at')->paginate($this->perPage);

        $this->serviceRequests = $this->applyFilters($query, 'created_at')->get();
    }

    private function loadMedicalHistories()
    {
        $query = \App\Models\MedicalHistory::where('patient_id', $this->patientId)->orderBy('recorded_date', 'desc');

        // $this->medicalHistories = $this->applyFilters($query, 'recorded_date')->paginate($this->perPage);

        $this->medicalHistories = $this->applyFilters($query, 'recorded_date')->get();
    }

    private function loadMedicalNotes()
    {
        $query = \App\Models\ClinicalImpression::where('patient_id', $this->patientId)->orderBy('created_at', 'desc');
        // $this->medicalHistories = $this->applyFilters($query, 'recorded_date')->paginate($this->perPage);

        $this->medicalNotes = $this->applyFilters($query, 'created_at')->get();
    }

    private function loadPersonalNotes()
    {
        if (auth()->user()->hasRole('doctor')) {
            $query = Note::wherePractitionerId(auth()->user()->practitioner->id)->where('patient_id', $this->patientId)->orderBy('created_at', 'desc');
        } elseif (auth()->user()->hasRole('admin')) {
            $query = Note::where('patient_id', $this->patientId)->orderBy('created_at', 'desc');
        }

        $this->personalNotes = $this->applyFilters($query, 'created_at')->get();
    }

    private function loadMedicalLeaves()
    {
        $query = MedicalLeave::where('patient_id', $this->patientId)
            ->with(['practitioner', 'condition'])
            ->orderBy('issue_date', 'desc');

        // Si es doctor, filtrar solo licencias médicas que él emitió
        if (auth()->user()->hasRole('doctor') && ! $this->completeAutorization) {
            $practitionerId = auth()->user()->practitioner->id;
            $query->where('practitioner_id', $practitionerId);
        }

        $allMedicalLeaves = $this->applyFilters($query, 'issue_date')->get();

        // Implementar paginación
        $this->totalMedicalLeavesCount = $allMedicalLeaves->count();
        $offset = ($this->currentMedicalLeavesPage - 1) * $this->encountersPerPage;
        $paginatedMedicalLeaves = $allMedicalLeaves->slice($offset, $this->encountersPerPage);

        $this->medicalLeaves = [
            'data' => $paginatedMedicalLeaves,
            'total' => $this->totalMedicalLeavesCount,
            'current_page' => $this->currentMedicalLeavesPage,
            'per_page' => $this->encountersPerPage,
            'last_page' => ceil($this->totalMedicalLeavesCount / $this->encountersPerPage),
            'from' => $this->totalMedicalLeavesCount > 0 ? $offset + 1 : 0,
            'to' => min($offset + $this->encountersPerPage, $this->totalMedicalLeavesCount),
        ];
    }

    // ===========================================
    // MÉTODOS DE FILTROS Y BÚSQUEDA
    // ===========================================

    private function applyFilters($query, $dateField = 'encounter_date')
    {
        // Filtro por rango de tiempo
        if ($this->selectedTimeRange !== 'all') {
            $query = match ($this->selectedTimeRange) {
                'today' => $query->whereDate($dateField, Carbon::today()),
                'week' => $query->whereBetween($dateField, [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]),
                'month' => $query->whereMonth($dateField, Carbon::now()->month),
                'year' => $query->whereYear($dateField, Carbon::now()->year),
                'custom' => $this->dateFrom && $this->dateTo ?
                    $query->whereBetween($dateField, [$this->dateFrom, $this->dateTo]) : $query,
                default => $query
            };
        }

        // Filtro por término de búsqueda
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('notes', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('description', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('diagnosis', 'like', '%'.$this->searchTerm.'%');
            });
        }

        return $query;
    }

    public function updatedSelectedTimeRange()
    {
        $this->resetPage();
        $this->reloadCurrentSection();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
        $this->reloadCurrentSection();
    }

    public function updatedConditionsSearchTerm()
    {
        $this->currentConditionsPage = 1;
        $this->reloadCurrentSection();
    }

    public function toggleFilters()
    {
        $this->showFilters = ! $this->showFilters;
    }

    public function clearFilters()
    {
        $this->selectedTimeRange = 'all';
        $this->searchTerm = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->selectedProvider = '';
        $this->selectedCondition = '';
        $this->resetPage();
        $this->reloadCurrentSection();
    }

    // ===========================================
    // MÉTODOS DE NAVEGACIÓN
    // ===========================================

    public function changeSection($section)
    {
        $this->activeSection = $section;
        $this->resetPage();

        if ($section != 'overview') {
            $this->loadSection($section);
        }
    }

    public function toggleGroupByEncounter()
    {
        $this->groupByEncounter = ! $this->groupByEncounter;
        $this->currentEncounterPage = 1; // Reset to first page when toggling
        // Recargar la sección actual si es medical-requests
        if ($this->activeSection === 'medical-requests') {
            $this->reloadCurrentSection();
        }
    }

    public function nextEncounterPage()
    {
        $lastPage = ceil($this->totalEncounters / $this->encountersPerPage);
        if ($this->currentEncounterPage < $lastPage) {
            $this->currentEncounterPage++;
            $this->reloadCurrentSection();
        }
    }

    public function previousEncounterPage()
    {
        if ($this->currentEncounterPage > 1) {
            $this->currentEncounterPage--;
            $this->reloadCurrentSection();
        }
    }

    public function gotoEncounterPage($page)
    {
        $lastPage = ceil($this->totalEncounters / $this->encountersPerPage);
        if ($page >= 1 && $page <= $lastPage) {
            $this->currentEncounterPage = $page;
            $this->reloadCurrentSection();
        }
    }

    public function toggleGroupPhysicalExamsByEncounter()
    {
        $this->groupPhysicalExamsByEncounter = ! $this->groupPhysicalExamsByEncounter;
        $this->currentPhysicalExamPage = 1; // Reset to first page when toggling
        // Recargar la sección actual si es physical-exams
        if ($this->activeSection === 'physical-exams') {
            $this->reloadCurrentSection();
        }
    }

    public function nextPhysicalExamPage()
    {
        $lastPage = ceil($this->totalPhysicalExamEncounters / $this->encountersPerPage);
        if ($this->currentPhysicalExamPage < $lastPage) {
            $this->currentPhysicalExamPage++;
            $this->reloadCurrentSection();
        }
    }

    public function previousPhysicalExamPage()
    {
        if ($this->currentPhysicalExamPage > 1) {
            $this->currentPhysicalExamPage--;
            $this->reloadCurrentSection();
        }
    }

    public function gotoPhysicalExamPage($page)
    {
        $lastPage = ceil($this->totalPhysicalExamEncounters / $this->encountersPerPage);
        if ($page >= 1 && $page <= $lastPage) {
            $this->currentPhysicalExamPage = $page;
            $this->reloadCurrentSection();
        }
    }

    public function toggleGroupVitalSignsByEncounter()
    {
        $this->groupVitalSignsByEncounter = ! $this->groupVitalSignsByEncounter;
        $this->currentVitalSignPage = 1; // Reset to first page when toggling
        // Recargar la sección actual si es vital-signs
        if ($this->activeSection === 'vital-signs') {
            $this->reloadCurrentSection();
        }
    }

    public function nextVitalSignPage()
    {
        $lastPage = ceil($this->totalVitalSignEncounters / $this->encountersPerPage);
        if ($this->currentVitalSignPage < $lastPage) {
            $this->currentVitalSignPage++;
            $this->reloadCurrentSection();
        }
    }

    public function previousVitalSignPage()
    {
        if ($this->currentVitalSignPage > 1) {
            $this->currentVitalSignPage--;
            $this->reloadCurrentSection();
        }
    }

    public function gotoVitalSignPage($page)
    {
        $lastPage = ceil($this->totalVitalSignEncounters / $this->encountersPerPage);
        if ($page >= 1 && $page <= $lastPage) {
            $this->currentVitalSignPage = $page;
            $this->reloadCurrentSection();
        }
    }

    public function nextEncountersPage()
    {
        $lastPage = ceil($this->totalEncountersCount / $this->encountersPerPage);
        if ($this->currentEncountersPage < $lastPage) {
            $this->currentEncountersPage++;
            $this->reloadCurrentSection();
        }
    }

    public function previousEncountersPage()
    {
        if ($this->currentEncountersPage > 1) {
            $this->currentEncountersPage--;
            $this->reloadCurrentSection();
        }
    }

    public function gotoEncountersPage($page)
    {
        $lastPage = ceil($this->totalEncountersCount / $this->encountersPerPage);
        if ($page >= 1 && $page <= $lastPage) {
            $this->currentEncountersPage = $page;
            $this->reloadCurrentSection();
        }
    }

    public function nextConditionsPage()
    {
        $lastPage = ceil($this->totalConditionsCount / $this->encountersPerPage);
        if ($this->currentConditionsPage < $lastPage) {
            $this->currentConditionsPage++;
            $this->reloadCurrentSection();
        }
    }

    public function previousConditionsPage()
    {
        if ($this->currentConditionsPage > 1) {
            $this->currentConditionsPage--;
            $this->reloadCurrentSection();
        }
    }

    public function gotoConditionsPage($page)
    {
        $lastPage = ceil($this->totalConditionsCount / $this->encountersPerPage);
        if ($page >= 1 && $page <= $lastPage) {
            $this->currentConditionsPage = $page;
            $this->reloadCurrentSection();
        }
    }

    public function nextPresentIllnessPage()
    {
        $lastPage = ceil($this->totalPresentIllnessCount / $this->encountersPerPage);
        if ($this->currentPresentIllnessPage < $lastPage) {
            $this->currentPresentIllnessPage++;
            $this->reloadCurrentSection();
        }
    }

    public function previousPresentIllnessPage()
    {
        if ($this->currentPresentIllnessPage > 1) {
            $this->currentPresentIllnessPage--;
            $this->reloadCurrentSection();
        }
    }

    public function gotoPresentIllnessPage($page)
    {
        $lastPage = ceil($this->totalPresentIllnessCount / $this->encountersPerPage);
        if ($page >= 1 && $page <= $lastPage) {
            $this->currentPresentIllnessPage = $page;
            $this->reloadCurrentSection();
        }
    }

    public function nextMedicalLeavesPage()
    {
        $lastPage = ceil($this->totalMedicalLeavesCount / $this->encountersPerPage);
        if ($this->currentMedicalLeavesPage < $lastPage) {
            $this->currentMedicalLeavesPage++;
            $this->reloadCurrentSection();
        }
    }

    public function previousMedicalLeavesPage()
    {
        if ($this->currentMedicalLeavesPage > 1) {
            $this->currentMedicalLeavesPage--;
            $this->reloadCurrentSection();
        }
    }

    public function gotoMedicalLeavesPage($page)
    {
        $lastPage = ceil($this->totalMedicalLeavesCount / $this->encountersPerPage);
        if ($page >= 1 && $page <= $lastPage) {
            $this->currentMedicalLeavesPage = $page;
            $this->reloadCurrentSection();
        }
    }

    private function reloadCurrentSection()
    {
        if ($this->activeSection !== 'overview') {
            // Remover de cargados para forzar recarga
            $this->sectionsLoaded = array_filter($this->sectionsLoaded, function ($section) {
                return $section !== $this->activeSection;
            });
            $this->loadSection($this->activeSection);
        }
    }

    // ===========================================
    // MÉTODOS AUXILIARES
    // ===========================================

    private function getRecentActivity()
    {
        $activities = collect();

        // Últimos encounters
        $encounters = Encounter::where('patient_id', $this->patientId)
            ->latest('created_at')
            ->take(3)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'encounter',
                    'icon' => '🏥',
                    'title' => 'Consulta médica',
                    'description' => $item->diagnosis ?? 'Consulta general',
                    'date' => $item->encounter_date,
                    'provider' => $item->practitioner->name ?? 'No especificado',
                ];
            });

        // Últimas condiciones
        $conditions = Condition::where('patient_id', $this->patientId)
            ->where('clinical_status', 'active')
            ->latest('onset_date')
            ->take(2)
            ->get()
            ->map(function ($item) {
                $description = $item->onset_info;
                if ($item->icd10Code) {
                    $description = $item->icd10Code->description_es;
                }

                return [
                    'type' => 'condition',
                    'icon' => '🩺',
                    'title' => 'Nueva condición',
                    'description' => $item->code.' | '.$description,
                    'date' => $item->onset_date,
                    'severity' => $item->severity,
                ];
            });

        return $activities->merge($encounters)->merge($conditions)
            ->sortByDesc('date')
            ->take(5)
            ->values();
    }

    private function getSectionData()
    {
        return match ($this->activeSection) {
            'encounters' => $this->encounters,
            'vital-signs' => $this->vitalSigns,
            'physical-exams' => $this->physicalExams,
            'conditions' => $this->conditions,
            'present-illnesses' => $this->presentIllnesses,
            'medical-requests' => $this->medicalRequests,
            'service-requests' => $this->serviceRequests,
            'medical-histories' => $this->medicalHistories,
            'medical-notes' => $this->medicalNotes,
            'personal-notes' => $this->personalNotes,
            'medical-leaves' => $this->medicalLeaves,
            default => null
        };
    }

    private function getTimelineData()
    {
        if ($this->activeSection === 'overview') {
            return $this->overviewData['recent_activity'] ?? [];
        }

        return collect();
    }

    // ===========================================
    // MÉTODOS PARA GRÁFICAS DE SIGNOS VITALES
    // ===========================================

    public function getVitalSignsChartData()
    {
        $query = VitalSign::where('patient_id', $this->patientId)
            ->with(['encounter'])
            ->orderBy('effective_date', 'asc');

        // Si es doctor, filtrar solo signos vitales de sus encounters
        if (auth()->user()->hasRole('doctor') && ! $this->completeAutorization) {
            $practitionerId = auth()->user()->practitioner->id;
            $query->whereHas('encounter', function ($q) use ($practitionerId) {
                $q->where('practitioner_id', $practitionerId);
            });
        }

        // Limitar a máximo 5 años de historial
        $query->where('effective_date', '>=', Carbon::now()->subYears(5));

        $vitalSigns = $query->get();

        // Organizar datos por fecha y tipo de signo vital
        $bloodPressureData = [];
        $heartRateData = [];
        $respiratoryRateData = [];

        foreach ($vitalSigns as $vital) {
            $date = Carbon::parse($vital->effective_date)->format('Y-m-d');

            switch ($vital->code) {
                case '8480-6': // Presión sistólica
                    if (! isset($bloodPressureData[$date])) {
                        $bloodPressureData[$date] = ['systolic' => null, 'diastolic' => null];
                    }
                    $bloodPressureData[$date]['systolic'] = (float) $vital->value;
                    break;
                case '8462-4': // Presión diastólica
                    if (! isset($bloodPressureData[$date])) {
                        $bloodPressureData[$date] = ['systolic' => null, 'diastolic' => null];
                    }
                    $bloodPressureData[$date]['diastolic'] = (float) $vital->value;
                    break;
                case '8867-4': // Frecuencia cardíaca
                    $heartRateData[$date] = (float) $vital->value;
                    break;
                case '9279-1': // Frecuencia respiratoria
                    $respiratoryRateData[$date] = (float) $vital->value;
                    break;
            }
        }

        // Filtrar datos nulos para blood pressure
        $filteredBloodPressureData = [];
        foreach ($bloodPressureData as $date => $values) {
            if ($values['systolic'] !== null && $values['diastolic'] !== null) {
                $filteredBloodPressureData[$date] = $values;
            }
        }

        // Preparar datos para ApexCharts
        return [
            'bloodPressure' => [
                'dates' => array_values(array_keys($filteredBloodPressureData)),
                'systolic' => array_values(array_column($filteredBloodPressureData, 'systolic')),
                'diastolic' => array_values(array_column($filteredBloodPressureData, 'diastolic')),
            ],
            'heartRate' => [
                'dates' => array_values(array_keys($heartRateData)),
                'values' => array_values($heartRateData),
            ],
            'respiratoryRate' => [
                'dates' => array_values(array_keys($respiratoryRateData)),
                'values' => array_values($respiratoryRateData),
            ],
        ];
    }

    public function updatedVitalSignsChartPeriod()
    {
        // Este método se llamará automáticamente cuando cambie el período
        // No necesita hacer nada, la vista se re-renderizará automáticamente
    }

    // ===========================================
    // MÉTODOS DE EXPORTACIÓN
    // ===========================================

    public function exportToPDF()
    {
        // Lógica para exportar historial médico a PDF
        $this->dispatch('export-pdf', patientId: $this->patientId);
    }

    public function exportToFHIR()
    {
        // Lógica para exportar en formato FHIR
        $this->dispatch('export-fhir', patientId: $this->patientId);
    }

    // ===========================================
    // LISTENERS DE EVENTOS
    // ===========================================

    #[On('noteAdded')]
    public function handleNoteAdded($type, $patientId)
    {
        // Verificar que el evento sea para este paciente
        if ($patientId != $this->patientId) {
            return;
        }

        // Recargar las notas correspondientes según el tipo
        if ($type === 'personal') {
            $this->loadPersonalNotes();

            // Si estamos en la sección de notas personales, forzar actualización
            if ($this->activeSection === 'personal-notes') {
                $this->dispatch('$refresh');
            }
        } elseif ($type === 'medical') {
            $this->loadMedicalNotes();

            // Si estamos en la sección de notas médicas, forzar actualización
            if ($this->activeSection === 'medical-notes') {
                $this->dispatch('$refresh');
            }
        }

        // También actualizar el overview si está cargado
        if (in_array('overview', $this->sectionsLoaded)) {
            $this->loadOverviewData();
        }
    }
}
