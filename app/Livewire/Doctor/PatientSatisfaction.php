<?php

namespace App\Livewire\Doctor;

use App\Models\Encounter;
use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class PatientSatisfaction extends Component
{
    public $timeFrame = '30'; // Por defecto últimos 30 días
    public $satisfactionData = [];
    public $averageRating = 0;
    public $totalReviews = 0;
    public $ratingDistribution = [];
    public $recentComments = [];
    public $order;
    public $isLoading = true;

    // Comentarios de ejemplo basados en ratings
    private $sampleComments = [
        5 => [
            'Excelente atención, muy profesional y amable.',
            'El doctor me explicó todo claramente, quedé muy satisfecho.',
            'Atención de primera calidad, lo recomiendo ampliamente.',
            'Muy buen trato y diagnóstico preciso.',
            'Profesional excepcional, resolvió todas mis dudas.'
        ],
        4 => [
            'Buena atención, aunque tuve que esperar un poco.',
            'El doctor fue amable y profesional.',
            'Quedé satisfecho con la consulta.',
            'Buen diagnóstico y tratamiento.',
            'Atención adecuada, volvería a consultar.'
        ],
        3 => [
            'Atención regular, podría mejorar la comunicación.',
            'La consulta fue correcta pero algo apresurada.',
            'Servicio promedio, cumplió con las expectativas.',
            'Atención básica, sin destacar particularmente.'
        ],
        2 => [
            'La consulta fue muy rápida, esperaba más tiempo.',
            'Podría mejorar la explicación del diagnóstico.',
            'Atención algo fría, falta calidez humana.'
        ],
        1 => [
            'No quedé satisfecho con la consulta.',
            'Esperaba mejor atención y más tiempo.',
            'La consulta fue muy apresurada.'
        ]
    ];

    public function mount()
    {
        // Inicializar variables para evitar errores
        $this->satisfactionData = [];
        $this->averageRating = 0;
        $this->totalReviews = 0;
        $this->ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $this->recentComments = [];
    }

    public function loadData()
    {
        $this->loadSatisfactionData();
        $this->isLoading = false;
    }

    public function updatedTimeFrame()
    {
        $this->loadSatisfactionData();
    }

    public function loadSatisfactionData()
    {
        $practitionerId = auth()->user()->practitioner->id;
        $days = (int) $this->timeFrame;

        // Obtener encounters finalizados
        $encounters = Encounter::query()
            ->where('practitioner_id', $practitionerId)
            ->where('status', 'finished')
            ->when($days > 0, function($query) use ($days) {
                return $query->where('start', '>=', now()->subDays($days));
            })
            ->with(['patient'])
            ->get();

        $this->totalReviews = $encounters->count();

        if ($this->totalReviews > 0) {
            $this->generateSatisfactionData($encounters);
        } else {
            $this->resetSatisfactionData();
        }
    }

    private function generateSatisfactionData($encounters)
    {
        $ratings = [];
        $this->ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $this->recentComments = [];

        foreach ($encounters as $encounter) {
            // Simular rating basado en factores como duración, especialidad, etc.
            $rating = $this->generateRealisticRating($encounter);
            $ratings[] = $rating;
            $this->ratingDistribution[$rating]++;

            // Generar comentarios para los primeros 5 encounters
            if (count($this->recentComments) < 5) {
                $this->recentComments[] = [
                    'rating' => $rating,
                    'comment' => $this->getRandomComment($rating),
                    'patient_name' => $encounter->patient->name ?? 'Paciente Anónimo',
                    'date' => Carbon::parse($encounter->start)->format('d/m/Y'),
                    'encounter_id' => $encounter->id
                ];
            }
        }

        $this->averageRating = round(array_sum($ratings) / count($ratings), 1);

        // Calcular porcentajes para la distribución
        foreach ($this->ratingDistribution as $rating => $count) {
            $this->ratingDistribution[$rating] = [
                'count' => $count,
                'percentage' => round(($count / $this->totalReviews) * 100, 1)
            ];
        }
    }

    private function generateRealisticRating($encounter)
    {
        // Simulación de rating basada en factores realistas
        $baseRating = 4; // La mayoría de doctores tienen buenas calificaciones

        // Factores que pueden influir en el rating
        $factors = [
            'duration' => $this->calculateDurationFactor($encounter),
            'specialty' => $this->calculateSpecialtyFactor($encounter),
            'time_of_day' => $this->calculateTimeOfDayFactor($encounter),
            'random' => mt_rand(-1, 1) * 0.5 // Factor aleatorio
        ];

        $finalRating = $baseRating + array_sum($factors);

        // Asegurar que esté entre 1 y 5
        return max(1, min(5, round($finalRating)));
    }

    private function calculateDurationFactor($encounter)
    {
        // Encounters más largos tienden a tener mejor rating
        if ($encounter->start && $encounter->end) {
            $duration = Carbon::parse($encounter->end)->diffInMinutes(Carbon::parse($encounter->start));
            if ($duration > 30) return 0.5;
            if ($duration < 15) return -0.5;
        }
        return 0;
    }

    private function calculateSpecialtyFactor($encounter)
    {
        // Algunas especialidades pueden tener diferentes patrones de satisfacción
        return mt_rand(-2, 3) * 0.1; // Variación pequeña
    }

    private function calculateTimeOfDayFactor($encounter)
    {
        // Horarios pico pueden tener ratings ligeramente menores
        $hour = Carbon::parse($encounter->start)->hour;
        if ($hour >= 9 && $hour <= 11) return 0.2; // Mañana
        if ($hour >= 14 && $hour <= 16) return 0.1; // Tarde temprano
        if ($hour >= 17) return -0.1; // Tarde tardía
        return 0;
    }

    private function getRandomComment($rating)
    {
        $comments = $this->sampleComments[$rating] ?? $this->sampleComments[3];
        return $comments[array_rand($comments)];
    }

    private function resetSatisfactionData()
    {
        $this->averageRating = 0;
        $this->ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        $this->recentComments = [];
    }

    public function getRatingColor($rating)
    {
        $colors = [
            1 => '#dc3545', // Rojo
            2 => '#fd7e14', // Naranja
            3 => '#ffc107', // Amarillo
            4 => '#20c997', // Verde claro
            5 => '#28a745'  // Verde
        ];

        return $colors[$rating] ?? '#6c757d';
    }

    public function getOverallRatingColor()
    {
        if ($this->averageRating >= 4.5) return '#28a745';
        if ($this->averageRating >= 4.0) return '#20c997';
        if ($this->averageRating >= 3.5) return '#ffc107';
        if ($this->averageRating >= 3.0) return '#fd7e14';
        return '#dc3545';
    }

    public function render()
    {
        return view('livewire.doctor.patient-satisfaction');
    }
}
