<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use App\Models\PresentIllnesType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class PresentIllness extends Component
{
    public $present_illness;

    public $reason;

    public $encounter_id;

    public $encounter;

    public $location = [];

    public $severity;

    public $duration;

    public $timing;

    public $description;

    public $aggravating_factors;

    public $alleviating_factors;

    public $associated_symptoms;

    public $items = [];

    public $mode = 'full'; // 'full' or 'simplified'

    protected $listeners = ['voice-dictation-present-illness' => 'updateFromVoice'];

    public function mount()
    {
        $this->encounter = Encounter::find($this->encounter_id);
        $this->present_illness = $this->encounter->presentIllnesses;
        $this->loadPresentIllnessMode();
        $this->loadPressentIllness();
    }

    /**
     * Update present illness from voice dictation
     */
    public function updateFromVoice($data)
    {
        $updated = false;

        // Update locations (array)
        if (isset($data['locations']) && is_array($data['locations']) && ! empty($data['locations'])) {
            // Map Spanish location names to database values
            $mappedLocations = $this->mapLocationsFromVoice($data['locations']);
            if (! empty($mappedLocations)) {
                $this->location = ['location' => $mappedLocations];
                $updated = true;
            }
        }

        // Update description
        if (isset($data['description']) && ! empty($data['description'])) {
            $this->description = $data['description'];
            $updated = true;
        }

        // Update severity (map Spanish to English)
        if (isset($data['severity']) && ! empty($data['severity'])) {
            $this->severity = $this->mapSeverityFromVoice($data['severity']);
            $updated = true;
        }

        // Update duration (map Spanish to English)
        if (isset($data['duration']) && ! empty($data['duration'])) {
            $this->duration = $this->mapDurationFromVoice($data['duration']);
            $updated = true;
        }

        // Update timing (map Spanish to English)
        if (isset($data['timing']) && ! empty($data['timing'])) {
            $this->timing = $this->mapTimingFromVoice($data['timing']);
            $updated = true;
        }

        // Update aggravating factors
        if (isset($data['aggravating_factors']) && ! empty($data['aggravating_factors'])) {
            $this->aggravating_factors = $data['aggravating_factors'];
            $updated = true;
        }

        // Update alleviating factors
        if (isset($data['alleviating_factors']) && ! empty($data['alleviating_factors'])) {
            $this->alleviating_factors = $data['alleviating_factors'];
            $updated = true;
        }

        // Update associated symptoms
        if (isset($data['associated_symptoms']) && ! empty($data['associated_symptoms'])) {
            $this->associated_symptoms = $data['associated_symptoms'];
            $updated = true;
        }

        // Save if any field was updated
        if ($updated) {
            // Create present illness if it doesn't exist
            if (! $this->encounter->presentIllnesses->id) {
                $this->create();
            }

            // Save all fields
            if ($this->description) {
                $this->saveDescription();
            }
            if ($this->aggravating_factors) {
                $this->saveAggravatingFactors();
            }
            if ($this->alleviating_factors) {
                $this->saveAlleviatingFactors();
            }
            if ($this->associated_symptoms) {
                $this->saveAssociatedSymptoms();
            }

            // Save locations
            if (isset($this->location['location']) && ! empty($this->location['location'])) {
                foreach ($this->location['location'] as $loc) {
                    $this->save('location', $loc);
                }
            }

            // Save severity, duration, timing
            if ($this->severity || $this->duration || $this->timing) {
                $this->encounter->presentIllnesses->severity = $this->severity;
                $this->encounter->presentIllnesses->duration = $this->duration;
                $this->encounter->presentIllnesses->timing = $this->timing;
                $this->encounter->presentIllnesses->save();
            }

            $this->loadPressentIllness();
        }
    }

    /**
     * Map locations from voice dictation to database values
     */
    private function mapLocationsFromVoice(array $locations): array
    {
        $mapped = [];
        $locationMap = $this->getLocationMapping();

        foreach ($locations as $location) {
            $normalized = strtolower(trim($location));

            // Direct match
            if (isset($locationMap[$normalized])) {
                $mapped[] = $locationMap[$normalized];
            } else {
                // Try fuzzy matching
                foreach ($locationMap as $key => $value) {
                    if (str_contains($key, $normalized) || str_contains($normalized, $key)) {
                        $mapped[] = $value;
                        break;
                    }
                }
            }
        }

        return array_unique($mapped);
    }

    /**
     * Get location mapping from Spanish to English database values
     */
    private function getLocationMapping(): array
    {
        return [
            'cabeza' => 'head',
            'cuello' => 'neck',
            'garganta' => 'throat',
            'pecho' => 'chest',
            'abdomen' => 'abdomen',
            'estómago' => 'stomach',
            'estomago' => 'stomach',
            'corazón' => 'heart',
            'corazon' => 'heart',
            'pulmones' => 'lungs',
            'brazo' => 'arm(s)',
            'brazos' => 'arm(s)',
            'mano' => 'hand(s)',
            'manos' => 'hand(s)',
            'pierna' => 'leg(s)',
            'piernas' => 'leg(s)',
            'pie' => 'foot/feet',
            'pies' => 'foot/feet',
            'rodilla' => 'knee(s)',
            'rodillas' => 'knee(s)',
            'hombro' => 'shoulder(s)',
            'hombros' => 'shoulder(s)',
            'espalda' => 'dorsal spine',
            'lumbar' => 'lumbar spine',
            'ojo' => 'eye(s)',
            'ojos' => 'eye(s)',
            'oído' => 'ear(s)',
            'oido' => 'ear(s)',
            'oídos' => 'ear(s)',
            'oidos' => 'ear(s)',
            'nariz' => 'nose',
            'boca' => 'mouth',
            'tobillo' => 'ankle(s)',
            'tobillos' => 'ankle(s)',
            'muñeca' => 'wrist(s)',
            'muñecas' => 'wrist(s)',
            'codo' => 'elbow(s)',
            'codos' => 'elbow(s)',
            'cadera' => 'hip',
            'muslo' => 'thighs',
            'muslos' => 'thighs',
            'todo el cuerpo' => 'full body',
            'cuerpo completo' => 'full body',
        ];
    }

    /**
     * Map severity from voice dictation to database values
     */
    private function mapSeverityFromVoice(string $severity): ?string
    {
        $severityMap = [
            'leve' => 'mild',
            'ligero' => 'mild',
            'ligera' => 'mild',
            'moderado' => 'moderate',
            'moderada' => 'moderate',
            'severo' => 'severe',
            'severa' => 'severe',
            'grave' => 'severe',
            'intenso' => 'severe',
            'intensa' => 'severe',
            'incapacitante' => 'disabling',
        ];

        $normalized = strtolower(trim($severity));

        return $severityMap[$normalized] ?? null;
    }

    /**
     * Map timing from voice dictation to database values
     */
    private function mapTimingFromVoice(string $timing): ?string
    {
        $timingMap = [
            'constante' => 'all day',
            'todo el día' => 'all day',
            'todo el dia' => 'all day',
            'intermitente' => 'through the day',
            'durante el día' => 'through the day',
            'durante el dia' => 'through the day',
            'en la mañana' => 'in the morning',
            'en la manana' => 'in the morning',
            'por la mañana' => 'in the morning',
            'por la manana' => 'in the morning',
            'en la tarde' => 'in the afternoon',
            'por la tarde' => 'in the afternoon',
            'en la noche' => 'at night',
            'por la noche' => 'at night',
        ];

        $normalized = strtolower(trim($timing));

        return $timingMap[$normalized] ?? null;
    }

    /**
     * Map duration from voice dictation to database values
     */
    private function mapDurationFromVoice(string $duration): ?string
    {
        $durationMap = [
            'unos días' => 'a few days ago',
            'unos dias' => 'a few days ago',
            'hace unos días' => 'a few days ago',
            'hace unos dias' => 'a few days ago',
            'una semana' => 'since a week ago',
            'hace una semana' => 'since a week ago',
            'dos semanas' => 'since two weeks ago',
            'hace dos semanas' => 'since two weeks ago',
            'tres semanas' => 'since three weeks ago',
            'hace tres semanas' => 'since three weeks ago',
            'un mes' => 'a month ago',
            'hace un mes' => 'a month ago',
            'algunos meses' => 'a couple of months',
            'hace algunos meses' => 'a couple of months',
            'seis meses' => 'past 6 months',
            'hace seis meses' => 'past 6 months',
            'un año' => 'a year or so',
            'un ano' => 'a year or so',
            'hace un año' => 'a year or so',
            'hace un ano' => 'a year or so',
            'año y medio' => 'a year and a half',
            'ano y medio' => 'a year and a half',
            'dos años' => 'two years',
            'dos anos' => 'two years',
            'más de dos años' => 'more than 2 years',
            'mas de dos anos' => 'more than 2 years',
        ];

        $normalized = strtolower(trim($duration));

        // Try direct match first
        if (isset($durationMap[$normalized])) {
            return $durationMap[$normalized];
        }

        // Try fuzzy matching
        foreach ($durationMap as $key => $value) {
            if (str_contains($normalized, $key) || str_contains($key, $normalized)) {
                return $value;
            }
        }

        return null;
    }

    public function loadPresentIllnessMode()
    {
        // Obtener la sección de Present Illness
        $presentIllnessSection = EncounterSection::where('name', 'Present Illness')->first();

        if ($presentIllnessSection) {
            // Buscar el template del usuario para esta sección
            $template = EncounterTemplate::where('user_id', Auth::id())
                ->where('encounter_section_id', $presentIllnessSection->id)
                ->first();

            // Si existe template y tiene configuración de modo, usarla
            if ($template && isset($template->encounter_section_fields['present_illness_mode'])) {
                $this->mode = $template->encounter_section_fields['present_illness_mode'];
            }
        }
    }

    public function loadPressentIllness()
    {
        $arr = ['location' => 'Ubicación', 'severity' => 'Gravedad', 'duration' => 'Duración', 'timing' => 'Momento'];
        $this->location = [];
        foreach ($arr as $key => $value) {
            $this->items[$key]['title'] = $value;
            $this->items[$key]['items'] = PresentIllnesType::whereType($key)->orderBy('value_esp')->get();
            if ($this->present_illness) {
                if ($key == 'location') {
                    $this->location[$key] = $this->encounter->presentIllnesses->locations;
                } else {
                    $this->$key = $this->encounter->presentIllnesses->$key;
                }
            }
        }

        if ($this->present_illness) {
            $this->description = $this->present_illness->description;
            $this->aggravating_factors = $this->present_illness->aggravating_factors;
            $this->alleviating_factors = $this->present_illness->alleviating_factors;
            $this->associated_symptoms = $this->present_illness->associated_symptoms;
        }
    }

    public function render()
    {
        return view('livewire.consultation.present-illness');
    }

    public function create($initialLocations = [])
    {
        $this->present_illness = $this->encounter->presentIllnesses()->create([
            'fhir_id' => 'condition-'.Str::uuid(),
            'description' => '',
            'location' => null,
            'locations' => $initialLocations,
            'severity' => $this->severity,
            'duration' => $this->duration,
            'timing' => $this->timing,
            'patient_id' => $this->encounter->patient_id,
            'practitioner_id' => $this->encounter->practitioner_id,
        ]);
    }

    public function save($type, $value, $multiple = false)
    {

        if ($type == 'severity') {
            $this->severity = $value;
        }
        if ($type == 'duration') {
            $this->duration = $value;
        }
        if ($type == 'timing') {
            $this->timing = $value;
        }

        if (! isset($this->encounter->presentIllnesses->fhir_id)) {
            // Si es location, crear con el valor inicial en el array locations
            if ($type == 'location') {
                $this->create([$value]);
            } else {
                $this->create();
            }
        } else {
            if ($type == 'location') {
                $locations = $this->encounter->presentIllnesses->addLocationIfMissing($value);
                $this->encounter->presentIllnesses->locations = $locations;
            }

            $this->encounter->presentIllnesses->severity = $this->severity;
            $this->encounter->presentIllnesses->duration = $this->duration;
            $this->encounter->presentIllnesses->timing = $this->timing;
            $this->encounter->presentIllnesses->save();

        }

        $this->loadPressentIllness();
        $this->dispatch('findFinishedButtonStatus');
    }

    public function delete($type, $value)
    {

        $locations = [];
        foreach ($this->encounter->presentIllnesses->locations as $l) {
            if ($l != $value) {
                $locations[] = $l;
            }
        }

        $this->encounter->presentIllnesses->locations = $locations;
        $this->encounter->presentIllnesses->save();
        $this->loadPressentIllness();
        $this->dispatch('findFinishedButtonStatus');
    }

    public function updatedAggravatingFactors()
    {
        $this->saveAggravatingFactors();
    }

    public function updatedAlleviatingFactors()
    {
        $this->saveAlleviatingFactors();
    }

    public function updatedAssociatedSymptoms()
    {
        $this->saveAssociatedSymptoms();
    }

    public function updatedDescription()
    {
        $this->saveDescription();
    }

    public function saveAggravatingFactors()
    {
        $key = 'aggravating_factors';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses->id) {
                $this->create();
            } else {
                $this->present_illness->aggravating_factors = $this->aggravating_factors;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);

        } catch (\Exception $e) {
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }

    public function saveAlleviatingFactors()
    {
        $key = 'alleviating_factors';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses->id) {
                $this->create();
            } else {
                $this->present_illness->alleviating_factors = $this->alleviating_factors;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);
        } catch (\Exception $e) {
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }

    public function saveAssociatedSymptoms()
    {
        $key = 'associated_symptoms';
        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses->id) {
                $this->create();
            } else {
                $this->present_illness->associated_symptoms = $this->associated_symptoms;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);
        } catch (\Exception $e) {
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }

    public function saveDescription()
    {
        $key = 'description';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses->id) {
                $this->create();
            } else {
                $this->present_illness->description = $this->description;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);

            $this->dispatch('findFinishedButtonStatus');
        } catch (\Exception $e) {
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }
}
