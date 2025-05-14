<?php

namespace Database\Factories;

use App\Models\Medication;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MedicationFactory extends Factory
{
    protected $model = Medication::class;

    public function definition()
    {
        $medicationTypes = [
            'tablet' => ['form' => 'Tableta', 'dose' => $this->faker->numberBetween(50, 1000) . ' mg'],
            'capsule' => ['form' => 'Cápsula', 'dose' => $this->faker->numberBetween(50, 500) . ' mg'],
            'injection' => ['form' => 'Inyección', 'dose' => $this->faker->numberBetween(1, 10) . ' mL'],
            'cream' => ['form' => 'Crema', 'dose' => $this->faker->numberBetween(1, 5) . '%'],
            'syrup' => ['form' => 'Jarabe', 'dose' => $this->faker->numberBetween(5, 20) . ' mg/5mL'],
            'inhaler' => ['form' => 'Inhalador', 'dose' => $this->faker->numberBetween(50, 500) . ' mcg/dosis'],
            'drops' => ['form' => 'Gotas', 'dose' => $this->faker->numberBetween(0.1, 2) . '%'],
        ];

        $type = $this->faker->randomElement(array_keys($medicationTypes));
        $name = $this->generateMedicationName($type);

        return [
            'fhir_id' => 'medication-' . Str::uuid(),
            'code' => $this->generateRxNormCode(),
            'name' => $name,
            'form' => $medicationTypes[$type]['form'],
            'dose' => $medicationTypes[$type]['dose'],
            'strength' => $this->generateStrength($medicationTypes[$type]['dose']),
            'ingredient' => json_encode([
                [
                    'item' => $this->generateActiveIngredient(),
                    'strength' => $this->generateStrength($medicationTypes[$type]['dose'])
                ]
            ]),
            'manufacturer' => $this->faker->randomElement([
                'Pfizer',
                'Novartis',
                'Roche',
                'Merck',
                'GSK',
                'AstraZeneca',
                'Sanofi'
            ]),
            'is_brand' => $this->faker->boolean(70),
            'is_over_the_counter' => $this->faker->boolean(30),
            'status' => 'active',
        ];
    }

    // States adicionales para tipos específicos
    public function brand()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_brand' => true,
                'name' => $this->faker->unique()->brandName(),
            ];
        });
    }

    public function generic()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_brand' => false,
                'name' => $this->generateGenericName(),
            ];
        });
    }

    public function overTheCounter()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_over_the_counter' => true,
            ];
        });
    }

    public function prescriptionOnly()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_over_the_counter' => false,
            ];
        });
    }

    public function withForm(string $form)
    {
        $forms = [
            'tablet' => ['form' => 'Tableta', 'dose' => $this->faker->numberBetween(50, 1000) . ' mg'],
            'capsule' => ['form' => 'Cápsula', 'dose' => $this->faker->numberBetween(50, 500) . ' mg'],
            'injection' => ['form' => 'Inyección', 'dose' => $this->faker->numberBetween(1, 10) . ' mL'],
        ];

        return $this->state(function (array $attributes) use ($form, $forms) {
            return [
                'form' => $forms[$form]['form'],
                'dose' => $forms[$form]['dose'],
                'strength' => $this->generateStrength($forms[$form]['dose']),
            ];
        });
    }

    // Métodos auxiliares
    protected function generateMedicationName(string $type): string
    {
        $prefixes = [
            'tablet' => ['Acet', 'Amox', 'Cipro', 'Dex', 'Liso', 'Metro', 'Ome', 'Para', 'Sulfa'],
            'capsule' => ['Cef', 'Doxy', 'Erythro', 'Flucona', 'Itracona', 'Rifam'],
            'injection' => ['Ceftria', 'Genta', 'Insulin', 'Morph', 'Vanc'],
            'cream' => ['Clotri', 'Hydro', 'Keto', 'Mico', 'Nyst', 'Terra'],
            'syrup' => ['Ambrox', 'Bromo', 'Code', 'Dextro', 'Guaife', 'Levo'],
            'inhaler' => ['Albu', 'Budo', 'Fluti', 'Formo', 'Salm', 'Tiotro'],
            'drops' => ['Cyclo', 'Latan', 'Timo', 'Tobra'],
        ];

        $suffixes = [
            'tablet' => ['amin', 'cilin', 'floxacine', 'mycin', 'nazole', 'pril', 'terol', 'xetine'],
            'capsule' => ['axone', 'cycline', 'mycin', 'penem', 'phylline', 'xacin'],
            'injection' => ['caine', 'cycline', 'mycin', 'phylline', 'zepam', 'zolam'],
            'cream' => ['conazole', 'dazole', 'furate', 'nasone', 'sone', 'zole'],
            'syrup' => ['fenesin', 'metorphan', 'pheniramine', 'promethazine', 'tussin'],
            'inhaler' => ['meterol', 'nide', 'phylline', 'sone', 'terol'],
            'drops' => ['lol', 'nide', 'prost', 'sone'],
        ];

        $prefix = $this->faker->randomElement($prefixes[$type]);
        $suffix = $this->faker->randomElement($suffixes[$type]);

        return $prefix . $suffix;
    }

    protected function generateGenericName(): string
    {
        $stems = [
            'ace', 'adol', 'afil', 'asone', 'azep', 'barb', 'caine', 'cef', 'ciclo', 'cort', 'dipine',
            'done', 'epam', 'fenac', 'flur', 'gab', 'gest', 'glit', 'ine', 'lukast', 'mab', 'micin',
            'mycin', 'nacin', 'olol', 'oxacin', 'pramine', 'pril', 'profen', 'ridone', 'sartan',
            'semide', 'setron', 'statin', 'terol', 'thiazide', 'tidine', 'trel', 'triptyline', 'vir',
            'vudine', 'xaban', 'zepam', 'zodone', 'zolam', 'zosin'
        ];

        return ucfirst($this->faker->randomElement($stems)) . $this->faker->randomElement($stems);
    }

    protected function generateActiveIngredient(): string
    {
        $ingredients = [
            'Acetaminophen', 'Amoxicillin', 'Azithromycin', 'Ciprofloxacin', 'Dexamethasone',
            'Diazepam', 'Enalapril', 'Fluoxetine', 'Hydrochlorothiazide', 'Ibuprofen',
            'Insulin glargine', 'Levothyroxine', 'Lisinopril', 'Metformin', 'Omeprazole',
            'Paracetamol', 'Prednisone', 'Simvastatin', 'Tramadol', 'Warfarin'
        ];

        return $this->faker->randomElement($ingredients);
    }

    protected function generateRxNormCode(): string
    {
        // Simula un código RxNorm (6 dígitos)
        return 'RXNORM-' . $this->faker->unique()->numerify('######');
    }

    protected function generateStrength(string $dose): string
    {
        $parts = explode(' ', $dose);
        $amount = $parts[0];
        $unit = $parts[1];

        // Para formas farmacéuticas como cremas o soluciones
        if (str_contains($unit, '%') || str_contains($unit, '/')) {
            return $dose;
        }

        // Para tabletas, cápsulas, etc.
        return $amount . ' mg';
    }

    protected function generateBrandName(): string
    {
        $prefixes = ['Zyr', 'Xan', 'Pro', 'Cele', 'Lip', 'Nex', 'Pla', 'Vic', 'Syn', 'Adv'];
        $suffixes = ['tec', 'tor', 'cor', 'via', 'lex', 'max', 'din', 'lor', 'pan', 'tin'];

        return $this->faker->randomElement($prefixes) . $this->faker->randomElement($suffixes);
    }
}
