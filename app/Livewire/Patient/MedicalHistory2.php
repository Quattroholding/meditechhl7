<?php

namespace App\Livewire\Patient;

use App\Models\Condition;
use App\Models\Encounter;
use App\Models\MedicationRequest;
use App\Models\ClinicalImpression;
use App\Models\Note;
use App\Models\Patient;
use App\Models\PhysicalExam;
use App\Models\PresentIllness;
use App\Models\ServiceRequest;
use App\Models\VitalSign;
use Carbon\Carbon;
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

    // Configuración de paginación
    public $perPage = 10;

    protected $paginationTheme = 'bootstrap';

    protected $queryString = [
        'activeSection' => ['except' => 'overview'],
        'selectedTimeRange' => ['except' => 'all'],
        'searchTerm' => ['except' => ''],
        'page' => ['except' => 1]
    ];

    public function mount($patientId)
    {
        $this->patientId = $patientId;
        $this->patient = Patient::findOrFail($patientId);
        $this->loadOverviewData();
    }

    public function render()
    {
        if(request()->has('activeSection')){
            $this->activeSection = request()->get('activeSection');
            $this->loadSection($this->activeSection);
        }

        return view('livewire.patient.medical-history2' ,[
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
        $total_personal_notes=0;
        $totalNotes=0;


        if(auth()->user()->hasRole('doctor')){
            $totalNotes =ClinicalImpression::where('patient_id', $this->patientId)->wherePractitionerId(auth()->user()->practitioner->id)->count();
        }
        if(auth()->user()->hasRole('asistente')){
            $totalNotes =ClinicalImpression::where('patient_id', $this->patientId)->count();
        }

        if(Encounter::where('patient_id', $this->patientId)->count()>0)
            $current_medications = $this->patient->current_medications;

        if(auth()->user()->hasRole('doctor'))
            $total_personal_notes = Note::wherePractitionerId(auth()->user()->practitioner->id)->where('patient_id', $this->patientId)->count();

        // Cargar historiales médicos por categoría - convertir a arrays
        $medicalHistories = \App\Models\MedicalHistory::where('patient_id', $this->patientId)
            ->where('category','<>','allergy')
            ->orderBy('recorded_date', 'desc')
            ->get()
            ->groupBy('category')
            ->map(function($histories) {
                return $histories->map(function($history) {
                    return [
                        'id' => $history->id,
                        'title' => $history->title,
                        'description' => $history->description,
                        'recorded_date' => $history->recorded_date,
                        'category' => $history->category,
                        'clinical_status' => $history->clinical_status
                    ];
                })->toArray();
            })->toArray();

        // Convertir alergias a array
        $allergies = $this->patient->allergies()->get()->map(function($allergy) {
            return [
                'id' => $allergy->id,
                'title' => $allergy->title,
                'description' => $allergy->description,
                'category' => $allergy->category
            ];
        })->toArray();

        // Convertir medicamentos a array
        $medications = collect($current_medications)->map(function($medication) {
            return [
                'id' => $medication->id ?? null,
                'medicine_name' => $medication->medicine->full_name ?? 'N/A',
                'dosage_text' => $medication->dosage_text ?? ''
            ];
        })->toArray();

        // Resumen general del paciente
        $this->overviewData = [
            'total_encounters' => Encounter::where('patient_id', $this->patientId)->count(),
            'active_conditions' => Condition::where('patient_id', $this->patientId)->where('clinical_status', 'active')->count(),
            'last_visit' => Encounter::where('patient_id', $this->patientId)->latest('created_at')->first()?->created_at,
            'total_requests' => MedicationRequest::where('patient_id', $this->patientId)->count()+ServiceRequest::where('patient_id', $this->patientId)->count(),
            'vital_signs_count' => VitalSign::where('patient_id', $this->patientId)->count(),
            'allergies' => $allergies,
            'medications' => $medications,
            'total_notes' => $totalNotes,
            'total_personal_notes' => $total_personal_notes,
            'recent_activity' => $this->getRecentActivity(),
            'medical_histories' => $medicalHistories
        ];

        $this->sectionsLoaded['overview'] = true;
        $this->isLoading = false;
    }

    public function loadSection($section)
    {
        if (!in_array($section, $this->sectionsLoaded)) {
            return;
        }

        $this->isLoading = true;
        $this->activeSection = $section;

        match($section) {
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
            default => null
        };

        $this->sectionsLoaded[] = $section;
        $this->isLoading = false;
    }

    private function loadEncounters()
    {
        $query = Encounter::where('patient_id', $this->patientId)->with(['practitioner'])->orderBy('created_at', 'desc');

        //$this->encounters = $this->applyFilters($query,'created_at')->paginate($this->perPage);

        $this->encounters = $this->applyFilters($query, 'created_at')->get();
    }

    private function loadVitalSigns()
    {
        $query = VitalSign::where('patient_id', $this->patientId)->with('encounter')->orderBy('created_at', 'desc');

        //$this->vitalSigns = $this->applyFilters($query, 'created_at')->paginate($this->perPage);

        $this->vitalSigns = $this->applyFilters($query, 'created_at')->get();
    }

    private function loadPhysicalExams()
    {
        $query = PhysicalExam::where('patient_id', $this->patientId)->with(['encounter', 'practitioner'])->orderBy('created_at', 'desc');

        //$this->physicalExams = $this->applyFilters($query, 'created_at')->paginate($this->perPage);

        $this->physicalExams = $this->applyFilters($query, 'created_at')->get();
    }

    private function loadConditions()
    {
        $query = Condition::where('patient_id', $this->patientId)->with('encounter')->orderBy('onset_date', 'desc');

        //$this->conditions = $this->applyFilters($query, 'onset_date')->paginate($this->perPage);

        $this->conditions = $this->applyFilters($query, 'onset_date')->get();
    }

    private function loadPresentIllnesses()
    {
        $query = PresentIllness::where('patient_id', $this->patientId)
            ->with('encounter')
            ->orderBy('created_at', 'desc');

        //$this->presentIllnesses = $this->applyFilters($query, 'created_at')->paginate($this->perPage);

        $this->presentIllnesses = $this->applyFilters($query, 'created_at')->get();
    }

    private function loadMedicalRequests()
    {
        $query = MedicationRequest::where('patient_id', $this->patientId)
            ->with(['encounter', 'practitioner'])
            ->orderBy('created_at', 'desc');

       // $this->medicalRequests = $this->applyFilters($query, 'created_at')->paginate($this->perPage);


        $this->medicalRequests['types']['medications'] = $this->applyFilters($query, 'created_at')->get();

        $query = ServiceRequest::where('patient_id', $this->patientId)->with(['encounter', 'practitioner']) ->orderBy('created_at', 'desc');

        //$this->serviceRequests = $this->applyFilters($query, 'created_at')->paginate($this->perPage);

        $this->medicalRequests['types']['services'] = $this->applyFilters($query, 'created_at')->get();
    }

    private function loadServiceRequests()
    {
        $query = ServiceRequest::where('patient_id', $this->patientId)
            ->with(['encounter', 'practitioner'])
            ->orderBy('created_at', 'desc');

        //$this->serviceRequests = $this->applyFilters($query, 'created_at')->paginate($this->perPage);

        $this->serviceRequests = $this->applyFilters($query, 'created_at')->get();
    }

    private function loadMedicalHistories()
    {
        $query = \App\Models\MedicalHistory::where('patient_id', $this->patientId)->orderBy('recorded_date', 'desc');

        //$this->medicalHistories = $this->applyFilters($query, 'recorded_date')->paginate($this->perPage);

        $this->medicalHistories = $this->applyFilters($query, 'recorded_date')->get();
    }

    private function loadMedicalNotes()
    {
        $query = \App\Models\ClinicalImpression::where('patient_id', $this->patientId)->orderBy('created_at', 'desc');
        //$this->medicalHistories = $this->applyFilters($query, 'recorded_date')->paginate($this->perPage);

        $this->medicalNotes = $this->applyFilters($query, 'created_at')->get();
    }

    private function loadPersonalNotes()
    {
        if(auth()->user()->hasRole('doctor'))
            $query = Note::wherePractitionerId(auth()->user()->practitioner->id)->where('patient_id', $this->patientId)->orderBy('created_at', 'desc');

        $this->personalNotes = $this->applyFilters($query, 'created_at')->get();
    }

    // ===========================================
    // MÉTODOS DE FILTROS Y BÚSQUEDA
    // ===========================================

    private function applyFilters($query, $dateField = 'encounter_date')
    {
        // Filtro por rango de tiempo
        if ($this->selectedTimeRange !== 'all') {
            $query = match($this->selectedTimeRange) {
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
            $query->where(function($q) {
                $q->where('notes', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('diagnosis', 'like', '%' . $this->searchTerm . '%');
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

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
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
        //dd($section, $this->sectionsLoaded,in_array($section, $this->sectionsLoaded));
        if (in_array($section, $this->sectionsLoaded) && $section <> 'overview') {          ;
            $this->loadSection($section);
        }
    }

    private function reloadCurrentSection()
    {
        if ($this->activeSection !== 'overview') {
            // Remover de cargados para forzar recarga
            $this->sectionsLoaded = array_filter($this->sectionsLoaded, function($section) {
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
            ->map(function($item) {
                return [
                    'type' => 'encounter',
                    'icon' => '🏥',
                    'title' => 'Consulta médica',
                    'description' => $item->diagnosis ?? 'Consulta general',
                    'date' => $item->encounter_date,
                    'provider' => $item->practitioner->name ?? 'No especificado'
                ];
            });

        // Últimas condiciones
        $conditions = Condition::where('patient_id', $this->patientId)
            ->where('clinical_status', 'active')
            ->latest('onset_date')
            ->take(2)
            ->get()
            ->map(function($item) {
                return [
                    'type' => 'condition',
                    'icon' => '🩺',
                    'title' => 'Nueva condición',
                    'description' => $item->condition_name,
                    'date' => $item->onset_date,
                    'severity' => $item->severity
                ];
            });

        return $activities->merge($encounters)->merge($conditions)
            ->sortByDesc('date')
            ->take(5)
            ->values();
    }

    private function getSectionData()
    {
        return match($this->activeSection) {
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
}
