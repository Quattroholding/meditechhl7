<?php

namespace App\Livewire;

use App\Models\EncounterSection;
use App\Models\EncounterTemplate;
use Livewire\Component;

class ItemTransfer extends Component
{
    public $availableItems = [];

    public $selectedItems = [];

    public $client_id;

    public $category = null;

    public $sectionConfigs = []; // Almacena configuraciones de cada sección

    public function mount()
    {
        // Get user's practitioner and their specialties
        $user = auth()->user();
        $isAdmin = $user->hasRole('admin');
        $practitioner = $user->practitioner;
        $practitionerSpecialties = [];
        $isMedicalStudent = false;

        if ($practitioner) {
            $practitionerSpecialties = $practitioner->qualifications()
                ->pluck('medical_speciality_id')
                ->toArray();
            $isMedicalStudent = $practitioner->is_medical_student;
        }

        // Secciones restringidas para estudiantes de medicina
        $restrictedSectionsForStudents = [
            'Servicios Facturables',
            'Laboratorios',
            'Imagenes',
            'Medicamentos',
            'Procedimientos',
            'Referencia Especialista',
        ];

        // Get all sections filtered by specialty or admin
        $sectionsQuery = EncounterSection::query();

        // Apply category filter if provided
        if ($this->category) {
            $sectionsQuery->whereCategory($this->category);
        }

        if (! $isAdmin && $practitioner) {
            // Filter sections: show only those without specialty requirement OR matching practitioner's specialties
            $sectionsQuery->where(function ($query) use ($practitionerSpecialties) {
                $query->whereNull('medical_speciality_id')
                    ->orWhereIn('medical_speciality_id', $practitionerSpecialties);
            });

            // Excluir secciones restringidas si es estudiante de medicina
            if ($isMedicalStudent) {
                $sectionsQuery->whereNotIn('name_esp', $restrictedSectionsForStudents);
            }
        }

        $disponibles = $sectionsQuery->pluck('id');

        // Get obligatory sections
        $obligatorySections = EncounterSection::query()
            ->where('obligatory', 1)
            ->when($this->category, function ($query) {
                $query->whereCategory($this->category);
            })
            ->when(! $isAdmin && $practitioner, function ($query) use ($practitionerSpecialties, $isMedicalStudent, $restrictedSectionsForStudents) {
                $query->where(function ($q) use ($practitionerSpecialties) {
                    $q->whereNull('medical_speciality_id')
                        ->orWhereIn('medical_speciality_id', $practitionerSpecialties);
                });
                // Excluir secciones restringidas si es estudiante de medicina
                if ($isMedicalStudent) {
                    $query->whereNotIn('name_esp', $restrictedSectionsForStudents);
                }
            })
            ->pluck('id');

        $selecionados = collect();

        if ($user->clients()->first()) {
            $this->client_id = $user->clients()->first()->id;
            $selecionados = EncounterTemplate::whereClientId($this->client_id)
                ->whereUserId($user->id)
                ->whereType('client')
                ->pluck('encounter_section_id');

            // First time setup: If user has no configuration, add all general sections
            if ($selecionados->isEmpty()) {
                $generalSections = EncounterSection::query()
                    ->whereNull('medical_speciality_id')
                    ->when($this->category, function ($query) {
                        $query->whereCategory($this->category);
                    })
                    ->when($isMedicalStudent, function ($query) use ($restrictedSectionsForStudents) {
                        $query->whereNotIn('name_esp', $restrictedSectionsForStudents);
                    })
                    ->get();

                foreach ($generalSections as $section) {
                    EncounterTemplate::create([
                        'type' => 'client',
                        'client_id' => $this->client_id,
                        'user_id' => $user->id,
                        'encounter_section_id' => $section->id,
                    ]);
                }

                // Reload selected sections after initial setup
                $selecionados = EncounterTemplate::whereClientId($this->client_id)
                    ->whereUserId($user->id)
                    ->whereType('client')
                    ->pluck('encounter_section_id');
            }
        }

        // Merge obligatory sections with user selected sections
        $selecionados = $selecionados->merge($obligatorySections)->unique();

        // Cargar configuraciones de las secciones
        $this->loadSectionConfigs();

        if (count($selecionados) == 0) {
            $this->availableItems = $sectionsQuery->get()->toArray();
            $this->selectedItems = [];
        } else {
            $diff = $disponibles->diff($selecionados);

            $availableQuery = EncounterSection::query()
                ->whereIn('id', $diff);

            if ($this->category) {
                $availableQuery->whereCategory($this->category);
            }

            $this->availableItems = $availableQuery
                ->when(! $isAdmin && $practitioner, function ($query) use ($practitionerSpecialties, $isMedicalStudent, $restrictedSectionsForStudents) {
                    $query->where(function ($q) use ($practitionerSpecialties) {
                        $q->whereNull('medical_speciality_id')
                            ->orWhereIn('medical_speciality_id', $practitionerSpecialties);
                    });
                    // Excluir secciones restringidas si es estudiante de medicina
                    if ($isMedicalStudent) {
                        $query->whereNotIn('name_esp', $restrictedSectionsForStudents);
                    }
                })
                ->get()
                ->toArray();

            $selectedQuery = EncounterSection::query()
                ->whereIn('id', $selecionados);

            if ($this->category) {
                $selectedQuery->whereCategory($this->category);
            }

            $this->selectedItems = $selectedQuery
                ->when(! $isAdmin && $practitioner, function ($query) use ($practitionerSpecialties, $isMedicalStudent, $restrictedSectionsForStudents) {
                    $query->where(function ($q) use ($practitionerSpecialties) {
                        $q->whereNull('medical_speciality_id')
                            ->orWhereIn('medical_speciality_id', $practitionerSpecialties);
                    });
                    // Excluir secciones restringidas si es estudiante de medicina
                    if ($isMedicalStudent) {
                        $query->whereNotIn('name_esp', $restrictedSectionsForStudents);
                    }
                })
                ->get()
                ->toArray();
        }
    }

    public function moveToSelected($itemId)
    {
        $item = collect($this->availableItems)->firstWhere('id', $itemId);

        if ($item) {
            // Verificar si es estudiante de medicina y la sección está restringida
            $practitioner = auth()->user()->practitioner;
            if ($practitioner && $practitioner->is_medical_student) {
                $restrictedSectionsForStudents = [
                    'Servicios Facturables',
                    'Laboratorios',
                    'Imagenes',
                    'Medicamentos',
                    'Procedimientos',
                    'Referencia Especialista',
                ];

                if (in_array($item['name_esp'], $restrictedSectionsForStudents)) {
                    $this->dispatch('showToastrItemTransfer',
                        type: 'error',
                        message: 'Esta sección no está disponible para estudiantes de medicina.'
                    );

                    return;
                }
            }

            $this->availableItems = array_values(array_filter($this->availableItems, fn ($i) => $i['id'] !== $itemId));
            $this->selectedItems[] = $item;
        }

        EncounterTemplate::create([
            'type' => 'client',
            'client_id' => $this->client_id,
            'user_id' => auth()->user()->id,
            'encounter_section_id' => $itemId,
        ]);

        $this->dispatch('showToastrItemTransfer',
            type: 'success',
            message: 'Seccion agregada con exito.'
        );
    }

    public function moveToAvailable($itemId)
    {
        $item = collect($this->selectedItems)->firstWhere('id', $itemId);

        if ($item) {

            if ($item['obligatory']) {
                $this->dispatch('showToastrItemTransfer',
                    type: 'error',
                    message: 'Esta sección es obligatoria no se puede eliminar.'
                );
            } else {
                $this->selectedItems = array_values(array_filter($this->selectedItems, fn ($i) => $i['id'] !== $itemId));
                $this->availableItems[] = $item;
                $field = EncounterTemplate::whereClientId($this->client_id)
                    ->whereUserId(auth()->user()->id)
                    ->whereEncounterSectionId($itemId)
                    ->first();
                $field->delete();
                $this->dispatch('showToastrItemTransfer',
                    type: 'success',
                    message: 'Seccion eliminada con exito.'
                );
            }
        }
    }

    public function updateOrder($orderedIds)
    {
        $this->selectedItems = collect($orderedIds)->map(function ($id) {
            return collect($this->selectedItems)->firstWhere('id', $id);
        })->toArray();
    }

    public function loadSectionConfigs()
    {
        $templates = EncounterTemplate::whereClientId($this->client_id)
            ->whereUserId(auth()->user()->id)
            ->whereType('client')
            ->get();

        foreach ($templates as $template) {
            $this->sectionConfigs[$template->encounter_section_id] = $template->encounter_section_fields ?? [];
        }
    }

    public function updatePresentIllnessMode($sectionId, $mode)
    {
        $template = EncounterTemplate::whereClientId($this->client_id)
            ->whereUserId(auth()->user()->id)
            ->whereEncounterSectionId($sectionId)
            ->first();

        if ($template) {
            $fields = $template->encounter_section_fields ?? [];
            $fields['present_illness_mode'] = $mode;
            $template->encounter_section_fields = $fields;
            $template->save();

            $this->sectionConfigs[$sectionId] = $fields;

            $this->dispatch('showToastrItemTransfer',
                type: 'success',
                message: 'Modo de Present Illness actualizado con éxito.'
            );
        }
    }

    public function render()
    {
        return view('livewire.item-transfer');
    }
}
