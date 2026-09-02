<?php

namespace App\Livewire\Consultation;

use App\Models\Encounter;
use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use App\Models\PresentIllnesType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
        Log::info('PresentIllness: updateFromVoice called', [
            'encounter_id' => $this->encounter_id,
            'data' => $data,
        ]);

        // Create or get present illness record FIRST
        if (! $this->encounter->presentIllnesses || ! $this->encounter->presentIllnesses->id) {
            Log::info('PresentIllness: Creating new record');
            $this->create();
            $this->encounter->refresh();
            $this->present_illness = $this->encounter->presentIllnesses;
        }

        $presentIllness = $this->encounter->presentIllnesses;

        // Update description
        if (isset($data['description']) && ! empty($data['description'])) {
            $presentIllness->description = $data['description'];
        }

        // Update severity (map Spanish to English)
        if (isset($data['severity']) && ! empty($data['severity'])) {
            $presentIllness->severity = $this->mapSeverityFromVoice($data['severity']);
        }

        // Update duration (map Spanish to English OR keep as is if no mapping found)
        if (isset($data['duration']) && ! empty($data['duration'])) {
            $mappedDuration = $this->mapDurationFromVoice($data['duration']);
            // If mapping returns null, keep the original Spanish text
            $presentIllness->duration = $mappedDuration ?? $data['duration'];

            Log::info('PresentIllness: Duration mapped', [
                'original' => $data['duration'],
                'mapped' => $presentIllness->duration,
            ]);
        }

        // Update timing (map Spanish to English)
        if (isset($data['timing']) && ! empty($data['timing'])) {
            $presentIllness->timing = $this->mapTimingFromVoice($data['timing']);
        }

        // Update locations (map Spanish to English and merge with existing)
        if (isset($data['locations']) && is_array($data['locations']) && ! empty($data['locations'])) {
            $mappedLocations = $this->mapLocationsFromVoice($data['locations']);

            // Get existing locations
            $existingLocations = is_array($presentIllness->locations) ? $presentIllness->locations : [];

            // Merge and deduplicate
            $allLocations = array_unique(array_merge($existingLocations, $mappedLocations));
            $presentIllness->locations = array_values($allLocations);

            Log::info('PresentIllness: Locations mapped', [
                'original' => $data['locations'],
                'mapped' => $mappedLocations,
                'saved' => $presentIllness->locations,
            ]);
        }

        // Update aggravating factors
        if (isset($data['aggravating_factors']) && ! empty($data['aggravating_factors'])) {
            $presentIllness->aggravating_factors = $data['aggravating_factors'];
        }

        // Update alleviating factors
        if (isset($data['alleviating_factors']) && ! empty($data['alleviating_factors'])) {
            $presentIllness->alleviating_factors = $data['alleviating_factors'];
        }

        // Update associated symptoms
        if (isset($data['associated_symptoms']) && ! empty($data['associated_symptoms'])) {
            $presentIllness->associated_symptoms = $data['associated_symptoms'];
        }

        // Save all changes in one go
        $presentIllness->save();

        Log::info('PresentIllness: Record saved', [
            'id' => $presentIllness->id,
            'locations' => $presentIllness->locations,
            'duration' => $presentIllness->duration,
            'severity' => $presentIllness->severity,
        ]);

        // Reload for display
        $this->loadPressentIllness();
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
            // Head and neck
            'cabeza' => 'head',
            'cuello' => 'neck',
            'garganta' => 'throat',
            'ojo' => 'eye(s)',
            'ojos' => 'eye(s)',
            'oído' => 'ear(s)',
            'oido' => 'ear(s)',
            'oídos' => 'ear(s)',
            'oidos' => 'ear(s)',
            'nariz' => 'nose',
            'boca' => 'mouth',

            // Chest and respiratory
            'pecho' => 'chest',
            'tórax' => 'chest',
            'torax' => 'chest',
            'hemitórax' => 'chest',
            'hemitorax' => 'chest',
            'hemitórax derecho' => 'right chest',
            'hemitorax derecho' => 'right chest',
            'hemitórax izquierdo' => 'left chest',
            'hemitorax izquierdo' => 'left chest',
            'pulmones' => 'lungs',
            'pulmón derecho' => 'right lung',
            'pulmon derecho' => 'right lung',
            'pulmón izquierdo' => 'left lung',
            'pulmon izquierdo' => 'left lung',
            'base pulmonar' => 'lung base',
            'base pulmonar derecha' => 'right lung base',
            'base pulmonar izquierda' => 'left lung base',

            // Cardiovascular
            'corazón' => 'heart',
            'corazon' => 'heart',

            // Abdomen
            'abdomen' => 'abdomen',
            'estómago' => 'stomach',
            'estomago' => 'stomach',

            // Extremities
            'brazo' => 'arm(s)',
            'brazos' => 'arm(s)',
            'brazo derecho' => 'right arm',
            'brazo izquierdo' => 'left arm',
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
            'tobillo' => 'ankle(s)',
            'tobillos' => 'ankle(s)',
            'muñeca' => 'wrist(s)',
            'muñecas' => 'wrist(s)',
            'codo' => 'elbow(s)',
            'codos' => 'elbow(s)',
            'cadera' => 'hip',
            'muslo' => 'thighs',
            'muslos' => 'thighs',

            // Back
            'espalda' => 'dorsal spine',
            'lumbar' => 'lumbar spine',

            // General
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
            // Days
            'un día' => 'a few days ago',
            'un dia' => 'a few days ago',
            'dos días' => 'a few days ago',
            'dos dias' => 'a few days ago',
            'tres días' => 'a few days ago',
            'tres dias' => 'a few days ago',
            'cuatro días' => 'a few days ago',
            'cuatro dias' => 'a few days ago',
            'cinco días' => 'a few days ago',
            'cinco dias' => 'a few days ago',
            'unos días' => 'a few days ago',
            'unos dias' => 'a few days ago',
            'hace unos días' => 'a few days ago',
            'hace unos dias' => 'a few days ago',
            'desde hace un día' => 'a few days ago',
            'desde hace dos días' => 'a few days ago',
            'desde hace tres días' => 'a few days ago',
            'desde hace cuatro días' => 'a few days ago',
            'desde hace cinco días' => 'a few days ago',

            // Weeks
            'una semana' => 'since a week ago',
            'hace una semana' => 'since a week ago',
            'desde hace una semana' => 'since a week ago',
            'dos semanas' => 'since two weeks ago',
            'hace dos semanas' => 'since two weeks ago',
            'tres semanas' => 'since three weeks ago',
            'hace tres semanas' => 'since three weeks ago',

            // Months
            'un mes' => 'a month ago',
            'hace un mes' => 'a month ago',
            'algunos meses' => 'a couple of months',
            'hace algunos meses' => 'a couple of months',
            'seis meses' => 'past 6 months',
            'hace seis meses' => 'past 6 months',

            // Years
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

        // Try partial matching for common patterns
        if (preg_match('/(un|dos|tres|cuatro|cinco|seis|siete|ocho|nueve|diez)\s+(día|dias|dia)/i', $normalized)) {
            return 'a few days ago';
        }

        if (preg_match('/(una|dos|tres|cuatro)\s+semana/i', $normalized)) {
            return 'since a week ago';
        }

        if (preg_match('/(uno|un|dos|tres|cuatro|cinco|seis)\s+(mes|meses)/i', $normalized)) {
            return 'a couple of months';
        }

        // Try fuzzy matching as last resort
        foreach ($durationMap as $key => $value) {
            if (str_contains($normalized, $key)) {
                return $value;
            }
        }

        // Return null to keep original Spanish text
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

            if (! $this->encounter->presentIllnesses || ! $this->encounter->presentIllnesses->id) {
                $this->create();
                // Refresh to get the newly created record
                $this->encounter->refresh();
                $this->present_illness = $this->encounter->presentIllnesses;
            }

            if ($this->encounter->presentIllnesses && $this->encounter->presentIllnesses->id) {
                $this->present_illness->aggravating_factors = $this->aggravating_factors;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);

        } catch (\Exception $e) {
            Log::error('Error guardando aggravating_factors', [
                'encounter_id' => $this->encounter_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }

    public function saveAlleviatingFactors()
    {
        $key = 'alleviating_factors';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses || ! $this->encounter->presentIllnesses->id) {
                $this->create();
                // Refresh to get the newly created record
                $this->encounter->refresh();
                $this->present_illness = $this->encounter->presentIllnesses;
            }

            if ($this->encounter->presentIllnesses && $this->encounter->presentIllnesses->id) {
                $this->present_illness->alleviating_factors = $this->alleviating_factors;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);
        } catch (\Exception $e) {
            Log::error('Error guardando alleviating_factors', [
                'encounter_id' => $this->encounter_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }

    public function saveAssociatedSymptoms()
    {
        $key = 'associated_symptoms';
        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses || ! $this->encounter->presentIllnesses->id) {
                $this->create();
                // Refresh to get the newly created record
                $this->encounter->refresh();
                $this->present_illness = $this->encounter->presentIllnesses;
            }

            if ($this->encounter->presentIllnesses && $this->encounter->presentIllnesses->id) {
                $this->present_illness->associated_symptoms = $this->associated_symptoms;
                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);
        } catch (\Exception $e) {
            Log::error('Error guardando associated_symptoms', [
                'encounter_id' => $this->encounter_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }

    public function saveDescription()
    {
        $key = 'description';

        try {
            // Delay para que el usuario vea el spinner "Guardando..."
            sleep(1);

            if (! $this->encounter->presentIllnesses || ! $this->encounter->presentIllnesses->id) {
                $this->create();
                // Refresh to get the newly created record
                $this->encounter->refresh();
                $this->present_illness = $this->encounter->presentIllnesses;
            }

            if ($this->encounter->presentIllnesses && $this->encounter->presentIllnesses->id) {
                $this->present_illness->description = $this->description;
                if (empty($this->present_illness->fhir_id)) {
                    $this->present_illness->fhir_id = 'condition-'.Str::uuid();
                }

                $this->present_illness->save();
            }

            $this->dispatch('saved-'.$key);

            $this->dispatch('findFinishedButtonStatus');
        } catch (\Exception $e) {
            Log::error('Error guardando description', [
                'encounter_id' => $this->encounter_id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('error-'.$key, $e->getMessage());
        }
    }
}
