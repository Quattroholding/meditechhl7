<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestResult;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ServiceRequestResult>
 */
class ServiceRequestResultFactory extends Factory
{
    protected $model = ServiceRequestResult::class;

    public function definition(): array
    {
        $fileTypes = [
            'application/pdf' => '.pdf',
            'application/msword' => '.doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
            'image/jpeg' => '.jpg',
            'image/png' => '.png',
            'text/plain' => '.txt'
        ];

        $selectedMimeType = $this->faker->randomElement(array_keys($fileTypes));
        $extension = $fileTypes[$selectedMimeType];
        $fileName = $this->faker->words(3, true) . $extension;
        $fileSize = $this->faker->numberBetween(1024, 5242880); // 1KB to 5MB
        $resultDate = $this->faker->dateTimeBetween('-1 month', 'now');

        return [
            'fhir_id' => $this->faker->unique()->uuid(),
            'service_request_id' => ServiceRequest::factory(),
            'patient_id' => Patient::factory(),
            'practitioner_id' => Practitioner::factory(),
            'status' => $this->faker->randomElement([
                'registered', 'partial', 'preliminary', 'final', 
                'amended', 'corrected', 'cancelled'
            ]),
            'result_type' => $this->faker->randomElement([
                'laboratory', 'pathology', 'radiology', 'cardiology',
                'endoscopy', 'ultrasound', 'ct-scan', 'mri'
            ]),
            'code' => $this->faker->numerify('###-##'),
            'code_system' => $this->faker->randomElement([
                'http://loinc.org',
                'http://snomed.info/sct',
                'local-system'
            ]),
            'code_display' => $this->faker->sentence(4),
            'file_path' => 'medical-results/' . $this->faker->year() . '/' . $this->faker->month() . '/' . $fileName,
            'file_name' => $fileName,
            'file_type' => $selectedMimeType,
            'file_size' => $fileSize,
            'file_hash' => hash('sha256', $this->faker->text(1000)),
            'metadata' => [
                'original_filename' => $fileName,
                'uploaded_by' => $this->faker->name(),
                'scanner_model' => $this->faker->optional()->company(),
                'resolution' => $this->faker->optional()->randomElement(['300dpi', '600dpi', '1200dpi']),
                'pages' => $this->faker->numberBetween(1, 10)
            ],
            'result_date' => $resultDate,
            'uploaded_at' => $this->faker->dateTimeBetween($resultDate, 'now'),
            'observations' => $this->faker->optional(0.7)->paragraph(),
            'notes' => $this->faker->optional(0.3)->sentence(),
            'interpretation' => $this->faker->optional(0.6)->randomElement([
                'normal', 'abnormal', 'high', 'low', 'critical'
            ]),
            'reference_range' => $this->faker->optional(0.4)->bothify('##.# - ##.# mg/dL'),
            'specimen_info' => $this->faker->optional(0.5)->randomElements([
                'specimen_type' => $this->faker->randomElement(['blood', 'urine', 'tissue', 'saliva']),
                'collection_date' => $this->faker->dateTimeBetween('-2 days', 'now')->format('Y-m-d'),
                'collection_method' => $this->faker->randomElement(['venipuncture', 'finger_stick', 'catheter'])
            ]),
            'version' => $this->faker->numberBetween(1, 3),
            'effective_date' => $resultDate,
            'issued_date' => $this->faker->dateTimeBetween($resultDate, 'now'),
        ];
    }

    public function laboratory(): static
    {
        return $this->state(fn (array $attributes) => [
            'result_type' => 'laboratory',
            'code' => $this->faker->numerify('LAB-###'),
            'code_display' => $this->faker->randomElement([
                'Complete Blood Count', 'Lipid Panel', 'Liver Function Tests',
                'Thyroid Function Tests', 'Glucose Test', 'Hemoglobin A1c'
            ]),
            'specimen_info' => [
                'specimen_type' => 'blood',
                'collection_date' => $this->faker->dateTimeBetween('-2 days', 'now')->format('Y-m-d'),
                'collection_method' => 'venipuncture'
            ],
            'reference_range' => $this->faker->bothify('##.# - ##.# mg/dL'),
        ]);
    }

    public function radiology(): static
    {
        return $this->state(fn (array $attributes) => [
            'result_type' => 'radiology',
            'code' => $this->faker->numerify('RAD-###'),
            'code_display' => $this->faker->randomElement([
                'Chest X-Ray', 'Abdominal CT', 'Brain MRI',
                'Mammography', 'Bone Scan', 'Ultrasound'
            ]),
            'file_type' => 'application/pdf',
            'file_name' => 'radiology_report_' . $this->faker->dateTime()->format('Y_m_d') . '.pdf',
            'specimen_info' => null,
            'metadata' => [
                'modality' => $this->faker->randomElement(['XR', 'CT', 'MR', 'US', 'MG']),
                'body_part' => $this->faker->randomElement(['chest', 'abdomen', 'head', 'pelvis']),
                'contrast' => $this->faker->boolean(30)
            ]
        ]);
    }

    public function pathology(): static
    {
        return $this->state(fn (array $attributes) => [
            'result_type' => 'pathology',
            'code' => $this->faker->numerify('PATH-###'),
            'code_display' => $this->faker->randomElement([
                'Tissue Biopsy', 'Cytology', 'Surgical Pathology',
                'Frozen Section', 'Immunohistochemistry'
            ]),
            'specimen_info' => [
                'specimen_type' => 'tissue',
                'collection_date' => $this->faker->dateTimeBetween('-1 week', 'now')->format('Y-m-d'),
                'collection_method' => 'biopsy'
            ]
        ]);
    }

    public function final(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'final'
        ]);
    }

    public function preliminary(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'preliminary'
        ]);
    }

    public function abnormal(): static
    {
        return $this->state(fn (array $attributes) => [
            'interpretation' => $this->faker->randomElement(['abnormal', 'high', 'low', 'critical']),
            'observations' => 'Resultado fuera de los valores normales. Se requiere seguimiento médico.'
        ]);
    }

    public function withPdf(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_type' => 'application/pdf',
            'file_name' => 'medical_result_' . $this->faker->dateTime()->format('Y_m_d') . '.pdf'
        ]);
    }
}
