<?php

namespace Database\Factories;

use App\Models\Encounter;
use App\Models\Media;
use App\Models\Patient;
use App\Models\Practitioner;
use App\Models\SkinLesion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    private $types = ['photo', 'video', 'audio'];

    private $modalities = ['dermoscopy', 'photograph', 'clinical'];

    private $views = ['anterior', 'posterior', 'close-up', 'lateral'];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $type = fake()->randomElement($this->types);
        $isPhoto = $type === 'photo';
        $isVideo = $type === 'video';

        return [
            'fhir_id' => 'media-'.uniqid(),
            'patient_id' => Patient::factory(),
            'encounter_id' => fake()->optional(0.9)->randomElement(Encounter::pluck('id')->toArray()),
            'practitioner_id' => fake()->optional(0.9)->randomElement(Practitioner::pluck('id')->toArray()),
            'subject_type' => SkinLesion::class,
            'subject_id' => SkinLesion::factory(),
            'identifier' => 'MED-'.strtoupper(fake()->bothify('???###')),
            'status' => 'completed',
            'type' => $type,
            'modality' => fake()->optional(0.8)->randomElement($this->modalities),
            'view' => fake()->optional(0.7)->randomElement($this->views),
            'content_type' => $this->getContentType($type),
            'file_path' => $this->getFilePath($type),
            'file_name' => $this->getFileName($type),
            'file_size' => fake()->numberBetween(100000, 5000000),
            'width' => $isPhoto || $isVideo ? fake()->numberBetween(640, 1920) : null,
            'height' => $isPhoto || $isVideo ? fake()->numberBetween(480, 1080) : null,
            'frames' => $isVideo ? fake()->numberBetween(30, 300) : null,
            'duration' => $isVideo ? fake()->randomFloat(2, 5, 60) : null,
            'note' => fake()->optional(0.5)->sentence(),
            'created_datetime' => fake()->dateTimeBetween('-1 year', 'now'),
            'issued_datetime' => fake()->optional(0.7)->dateTimeBetween('-1 year', 'now'),
            'device_name' => fake()->optional(0.6)->randomElement([
                ['name' => 'iPhone 13 Pro'],
                ['name' => 'Canon EOS R5'],
                ['name' => 'Dermatoscope DermLite DL4'],
                ['name' => 'Samsung Galaxy S21'],
            ]),
            'extension' => null,
        ];
    }

    private function getContentType(string $type): string
    {
        return match ($type) {
            'photo' => fake()->randomElement(['image/jpeg', 'image/png']),
            'video' => 'video/mp4',
            'audio' => 'audio/mpeg',
            default => 'application/octet-stream',
        };
    }

    private function getFilePath(string $type): string
    {
        $folder = match ($type) {
            'photo' => 'dermatology/photos',
            'video' => 'dermatology/videos',
            'audio' => 'dermatology/audio',
            default => 'dermatology',
        };

        return "{$folder}/".fake()->uuid().".{$this->getExtension($type)}";
    }

    private function getFileName(string $type): string
    {
        return fake()->uuid().".{$this->getExtension($type)}";
    }

    private function getExtension(string $type): string
    {
        return match ($type) {
            'photo' => fake()->randomElement(['jpg', 'png']),
            'video' => 'mp4',
            'audio' => 'mp3',
            default => 'bin',
        };
    }

    /**
     * Indicate that the media is a dermatoscopy photo.
     */
    public function dermoscopy(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'photo',
            'modality' => 'dermoscopy',
            'view' => 'close-up',
            'content_type' => 'image/jpeg',
        ]);
    }

    /**
     * Indicate that the media is a clinical photograph.
     */
    public function photograph(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'photo',
            'modality' => 'photograph',
            'content_type' => 'image/jpeg',
        ]);
    }

    /**
     * Indicate that the media is a video.
     */
    public function video(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'video',
            'content_type' => 'video/mp4',
            'frames' => fake()->numberBetween(30, 300),
            'duration' => fake()->randomFloat(2, 5, 60),
        ]);
    }
}
