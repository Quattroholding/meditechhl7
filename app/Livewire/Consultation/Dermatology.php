<?php

namespace App\Livewire\Consultation;

use App\Models\BodySiteMarking;
use App\Models\BodyStructure;
use App\Models\Encounter;
use App\Models\Media;
use App\Models\Patient;
use App\Models\SkinLesion;
use App\Models\SnomedBodySite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class Dermatology extends Component
{
    use WithFileUploads;

    public $encounter_id;

    public $encounter;

    public $patient_id;

    public $patient;

    // Body diagram
    public $bodyView = 'anterior';

    public $markings = [];

    // Lesion form
    public $showLesionModal = false;

    public $editingLesionId = null;

    // Body structure fields
    public $location_code = '';

    public $location_display = '';

    public $location_qualifier_code = '';

    public $location_qualifier_display = '';

    public $morphology_code = '';

    public $morphology_display = '';

    public $description = '';

    // Skin lesion fields
    public $lesion_type = 'nevus';

    public $color = 'brown';

    public $size_length_mm = '';

    public $size_width_mm = '';

    public $size_height_mm = '';

    public $asymmetry = 'symmetric';

    public $border = 'regular';

    public $color_variation = '';

    public $diameter = 'less_than_6mm';

    public $evolving = 'stable';

    public $risk_level = 'low';

    public $clinical_notes = '';

    public $requires_biopsy = false;

    public $requires_removal = false;

    public $next_follow_up_date = '';

    public $treatment_plan = '';

    // Body site marking fields
    public $position_x = 0;

    public $position_y = 0;

    public $marker_color = '#22C55E';

    public $marker_shape = 'circle';

    public $marker_label = '';

    // Photos
    public $photo;

    public $existingLesions = [];

    public $snomedBodySites = [];

    protected $rules = [
        'location_code' => 'required',
        'location_display' => 'required',
        'lesion_type' => 'required',
        'size_length_mm' => 'nullable|numeric|min:0|max:999',
        'size_width_mm' => 'nullable|numeric|min:0|max:999',
        'risk_level' => 'required|in:low,moderate,high,suspicious',
    ];

    public function mount($encounter_id = null, $patient_id = null)
    {
        if ($encounter_id) {
            $this->encounter = Encounter::find($encounter_id);
            $this->patient = Patient::find($this->encounter->patient_id);
        } elseif ($patient_id) {
            $this->patient = Patient::find($patient_id);
            // For standalone mode, get or create a virtual encounter for tracking
            $this->encounter = $this->getOrCreateVirtualEncounter();
        }

        $this->loadLesions();
        $this->loadMarkings();
        $this->loadSnomedBodySites();
    }

    /**
     * Get or create a virtual encounter for standalone dermatology tracking
     */
    private function getOrCreateVirtualEncounter()
    {
        // First, try to find an encounter that has lesions for this patient
        $encountersWithLesions = Encounter::where('patient_id', $this->patient->id)
            ->where('status', 'in-progress')
            ->whereHas('skinLesions')
            ->latest()
            ->first();

        if ($encountersWithLesions) {
            return $encountersWithLesions;
        }

        // If no encounter with lesions, try to find a dermatology-specific encounter
        $encounter = Encounter::where('patient_id', $this->patient->id)
            ->where('type', 'dermatology')
            ->where('status', 'in-progress')
            ->latest()
            ->first();

        // If no dermatology encounter, use any in-progress encounter
        if (! $encounter) {
            $encounter = Encounter::where('patient_id', $this->patient->id)
                ->where('status', 'in-progress')
                ->latest()
                ->first();
        }

        // If still no encounter exists, create a virtual one
        if (! $encounter) {
            // Get practitioner_id - required field
            $practitionerId = null;
            if (auth()->user()) {
                $practitionerId = Auth::user()->practitioner?->id;
            }

            // If user doesn't have a practitioner, use the first available one as fallback
            if (! $practitionerId) {
                $practitionerId = \App\Models\Practitioner::first()?->id;
            }

            // If still no practitioner found, throw exception
            if (! $practitionerId) {
                throw new \Exception('No practitioner available to create encounter. Please ensure at least one practitioner exists in the system.');
            }

            $encounter = Encounter::create([
                'fhir_id' => 'encounter-'.Str::uuid(),
                'patient_id' => $this->patient->id,
                'practitioner_id' => $practitionerId,
                'appointment_id' => null,
                'identifier' => 'DERM-'.str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT),
                'class' => 'AMB',
                'status' => 'in-progress',
                'start' => now(),
                'end' => now()->addHours(1),
                'type' => 'dermatology',
                'reason' => 'Seguimiento dermatológico de lesiones cutáneas',
            ]);
        }

        return $encounter;
    }

    public function loadSnomedBodySites()
    {
        $this->snomedBodySites = SnomedBodySite::active()
            ->ordered()
            ->get()
            ->groupBy('category')
            ->toArray();
    }

    public function loadLesions()
    {
        $this->existingLesions = SkinLesion::with(['bodyStructure', 'marking', 'media'])
            ->where('patient_id', $this->patient->id)
            ->where('encounter_id', $this->encounter->id)
            ->get()
            ->map(function ($lesion) {
                $array = $lesion->toArray();
                $array['risk_color'] = $lesion->risk_color;
                if (isset($array['body_structure'])) {
                    $array['body_structure']['full_location'] = $lesion->bodyStructure->full_location;
                }

                return $array;
            })
            ->toArray();
    }

    public function loadMarkings()
    {
        $this->markings = BodySiteMarking::whereHas('bodyStructure', function ($q) {
            $q->where('patient_id', $this->patient->id)
                ->where('encounter_id', $this->encounter->id)
                ->where('active', true);
        })
            ->where('body_view', $this->bodyView)
            ->where('is_visible', true)
            ->with(['skinLesion', 'bodyStructure'])
            ->get()
            ->map(fn ($m) => [
                'id' => $m->id,
                'x' => (float) $m->position_x,
                'y' => (float) $m->position_y,
                'color' => $m->marker_color,
                'shape' => $m->marker_shape,
                'size' => $m->marker_size,
                'label' => $m->label,
                'risk_level' => $m->skinLesion?->risk_level,
                'lesion_id' => $m->skinLesion?->id,
            ])
            ->toArray();
    }

    public function changeView($view)
    {
        $this->bodyView = $view;
        $this->loadMarkings();
    }

    public function updatedLocationCode($value)
    {
        if ($value) {
            $site = SnomedBodySite::where('snomed_code', $value)->first();
            if ($site) {
                $this->location_display = $site->display_es ?? $site->display;
            }
        }
    }

    public function addMarkerAt($x, $y)
    {
        $this->position_x = $x;
        $this->position_y = $y;
        $this->resetForm();
        $this->showLesionModal = true;
    }

    public function updateMarkerPosition($lesionId, $x, $y)
    {
        $lesion = SkinLesion::with('marking')->findOrFail($lesionId);

        if ($lesion->marking) {
            $lesion->marking->update([
                'position_x' => round($x, 4),
                'position_y' => round($y, 4),
            ]);

            $this->loadMarkings();

            session()->flash('message', 'Posición del marcador actualizada exitosamente.');
        }
    }

    public function editLesion($lesionId)
    {
        $lesion = SkinLesion::with(['bodyStructure', 'marking'])->findOrFail($lesionId);

        $this->editingLesionId = $lesionId;

        // Load body structure data
        $this->location_code = $lesion->bodyStructure->location_code;
        $this->location_display = $lesion->bodyStructure->location_display;
        $this->location_qualifier_code = $lesion->bodyStructure->location_qualifier_code ?? '';
        $this->location_qualifier_display = $lesion->bodyStructure->location_qualifier_display ?? '';
        $this->morphology_code = $lesion->bodyStructure->morphology_code ?? '';
        $this->morphology_display = $lesion->bodyStructure->morphology_display ?? '';
        $this->description = $lesion->bodyStructure->description ?? '';

        // Load skin lesion data
        $this->lesion_type = $lesion->lesion_type;
        $this->color = $lesion->color ?? 'brown';
        $this->size_length_mm = $lesion->size_length_mm;
        $this->size_width_mm = $lesion->size_width_mm;
        $this->size_height_mm = $lesion->size_height_mm ?? '';
        $this->asymmetry = $lesion->asymmetry ?? 'symmetric';
        $this->border = $lesion->border ?? 'regular';
        $this->color_variation = $lesion->color_variation ?? '';
        $this->diameter = $lesion->diameter ?? 'less_than_6mm';
        $this->evolving = $lesion->evolving ?? 'stable';
        $this->risk_level = $lesion->risk_level;
        $this->clinical_notes = $lesion->clinical_notes ?? '';
        $this->requires_biopsy = $lesion->requires_biopsy;
        $this->requires_removal = $lesion->requires_removal;
        $this->next_follow_up_date = $lesion->next_follow_up_date?->format('Y-m-d') ?? '';
        $this->treatment_plan = $lesion->treatment_plan ?? '';

        // Load marking data
        if ($lesion->marking) {
            $this->position_x = $lesion->marking->position_x;
            $this->position_y = $lesion->marking->position_y;
            $this->marker_color = $lesion->marking->marker_color;
            $this->marker_shape = $lesion->marking->marker_shape;
            $this->marker_label = $lesion->marking->label ?? '';
            $this->bodyView = $lesion->marking->body_view;
        }

        $this->showLesionModal = true;
    }

    public function saveLesion()
    {
        $this->validate();

        // Auto-calculate diameter based on size
        if ($this->size_length_mm > 6) {
            $this->diameter = 'greater_than_6mm';
        } else {
            $this->diameter = 'less_than_6mm';
        }

        if ($this->editingLesionId) {
            $this->updateExistingLesion();
        } else {
            $this->createNewLesion();
        }

        $this->showLesionModal = false;
        $this->resetForm();
        $this->loadLesions();
        $this->loadMarkings();

        $this->dispatch('lesion-saved');
        session()->flash('message', 'Lesión guardada exitosamente.');
    }

    private function createNewLesion()
    {
        // Create body structure
        $bodyStructure = BodyStructure::create([
            'patient_id' => $this->patient->id,
            'encounter_id' => $this->encounter->id,
            'location_code' => $this->location_code,
            'location_display' => $this->location_display,
            'location_qualifier_code' => $this->location_qualifier_code ?: null,
            'location_qualifier_display' => $this->location_qualifier_display ?: null,
            'morphology_code' => $this->morphology_code ?: null,
            'morphology_display' => $this->morphology_display ?: null,
            'description' => $this->description ?: null,
            'active' => true,
        ]);

        // Create skin lesion
        $skinLesion = SkinLesion::create([
            'body_structure_id' => $bodyStructure->id,
            'patient_id' => $this->patient->id,
            'encounter_id' => $this->encounter->id,
            'practitioner_id' => Auth::user()->practitioner->id ?? $this->encounter->practitioner_id,
            'lesion_type' => $this->lesion_type,
            'color' => $this->color,
            'size_length_mm' => $this->size_length_mm ?: null,
            'size_width_mm' => $this->size_width_mm ?: null,
            'size_height_mm' => $this->size_height_mm ?: null,
            'asymmetry' => $this->asymmetry,
            'border' => $this->border,
            'color_variation' => $this->color_variation ?: null,
            'diameter' => $this->diameter,
            'evolving' => $this->evolving,
            'risk_level' => $this->risk_level,
            'clinical_notes' => $this->clinical_notes ?: null,
            'requires_biopsy' => $this->requires_biopsy,
            'requires_removal' => $this->requires_removal,
            'next_follow_up_date' => $this->next_follow_up_date ?: null,
            'status' => 'active',
            'treatment_plan' => $this->treatment_plan ?: null,
        ]);

        // Set marker color based on lesion color
        $this->marker_color = $this->getLesionMarkerColor($this->color);

        // Create body site marking
        BodySiteMarking::create([
            'body_structure_id' => $bodyStructure->id,
            'skin_lesion_id' => $skinLesion->id,
            'body_view' => $this->bodyView,
            'position_x' => $this->position_x,
            'position_y' => $this->position_y,
            'marker_color' => $this->marker_color,
            'marker_shape' => $this->marker_shape,
            'marker_size' => 12,
            'label' => $this->marker_label ?: null,
            'is_visible' => true,
        ]);

        // Handle photo upload
        if ($this->photo) {
            $this->uploadPhoto($skinLesion);
        }
    }

    private function updateExistingLesion()
    {
        $skinLesion = SkinLesion::with(['bodyStructure', 'marking'])->findOrFail($this->editingLesionId);

        // Update body structure
        $skinLesion->bodyStructure->update([
            'location_code' => $this->location_code,
            'location_display' => $this->location_display,
            'location_qualifier_code' => $this->location_qualifier_code ?: null,
            'location_qualifier_display' => $this->location_qualifier_display ?: null,
            'morphology_code' => $this->morphology_code ?: null,
            'morphology_display' => $this->morphology_display ?: null,
            'description' => $this->description ?: null,
        ]);

        // Update skin lesion
        $skinLesion->update([
            'lesion_type' => $this->lesion_type,
            'color' => $this->color,
            'size_length_mm' => $this->size_length_mm ?: null,
            'size_width_mm' => $this->size_width_mm ?: null,
            'size_height_mm' => $this->size_height_mm ?: null,
            'asymmetry' => $this->asymmetry,
            'border' => $this->border,
            'color_variation' => $this->color_variation ?: null,
            'diameter' => $this->diameter,
            'evolving' => $this->evolving,
            'risk_level' => $this->risk_level,
            'clinical_notes' => $this->clinical_notes ?: null,
            'requires_biopsy' => $this->requires_biopsy,
            'requires_removal' => $this->requires_removal,
            'next_follow_up_date' => $this->next_follow_up_date ?: null,
            'treatment_plan' => $this->treatment_plan ?: null,
        ]);

        // Update marking
        if ($skinLesion->marking) {
            $skinLesion->marking->update([
                'position_x' => $this->position_x,
                'position_y' => $this->position_y,
                'marker_color' => $this->getLesionMarkerColor($this->color),
                'marker_shape' => $this->marker_shape,
                'label' => $this->marker_label ?: null,
            ]);
        }

        // Handle photo upload
        if ($this->photo) {
            $this->uploadPhoto($skinLesion);
        }
    }

    private function uploadPhoto($skinLesion)
    {
        $path = $this->photo->store('dermatology/photos/'.$this->patient->id, 'public');

        Media::create([
            'patient_id' => $this->patient->id,
            'encounter_id' => $this->encounter->id,
            'practitioner_id' => Auth::user()->practitioner->id ?? $this->encounter->practitioner_id,
            'subject_type' => SkinLesion::class,
            'subject_id' => $skinLesion->id,
            'type' => 'photo',
            'modality' => 'photograph',
            'view' => $this->bodyView,
            'content_type' => $this->photo->getMimeType(),
            'file_path' => $path,
            'file_name' => $this->photo->getClientOriginalName(),
            'file_size' => $this->photo->getSize(),
            'created_datetime' => now(),
            'status' => 'completed',
        ]);
    }

    public function deleteLesion($lesionId)
    {
        $lesion = SkinLesion::findOrFail($lesionId);
        $lesion->delete(); // This will cascade delete marking and soft delete body structure

        $this->loadLesions();
        $this->loadMarkings();

        session()->flash('message', 'Lesión eliminada exitosamente.');
    }

    /**
     * Get hex color for lesion marker based on lesion color
     */
    private function getLesionMarkerColor($lesionColor)
    {
        $lesionColors = [
            'brown' => '#8B4513',
            'black' => '#000000',
            'red' => '#DC2626',
            'pink' => '#EC4899',
            'tan' => '#D2B48C',
            'blue' => '#2563EB',
            'purple' => '#9333EA',
            'white' => '#F3F4F6',
            'mixed' => '#6B7280',
        ];

        return $lesionColors[$lesionColor] ?? '#6B7280';
    }

    public function resetForm()
    {
        $this->editingLesionId = null;
        $this->location_code = '';
        $this->location_display = '';
        $this->location_qualifier_code = '';
        $this->location_qualifier_display = '';
        $this->morphology_code = '';
        $this->morphology_display = '';
        $this->description = '';
        $this->lesion_type = 'nevus';
        $this->color = 'brown';
        $this->size_length_mm = '';
        $this->size_width_mm = '';
        $this->size_height_mm = '';
        $this->asymmetry = 'symmetric';
        $this->border = 'regular';
        $this->color_variation = '';
        $this->diameter = 'less_than_6mm';
        $this->evolving = 'stable';
        $this->risk_level = 'low';
        $this->clinical_notes = '';
        $this->requires_biopsy = false;
        $this->requires_removal = false;
        $this->next_follow_up_date = '';
        $this->treatment_plan = '';
        $this->marker_label = '';
        $this->photo = null;
    }

    public function render()
    {
        return view('livewire.consultation.dermatology');
    }
}
