<?php

namespace App\Livewire\Doctor;

use App\Models\Encounter;
use Carbon\Carbon;
use Livewire\Component;

class ConsultationDurationTime extends Component
{
    public $loading = true;

    public $filter = 'daily'; // daily, weekly, monthly

    public $averageDuration = 0;

    public $chartData = [];

    public function mount()
    {
        $this->loading = true;
    }

    public function loadData()
    {
        $this->loading = false;
        $this->calculateDuration();
    }

    public function updatedFilter()
    {
        $this->calculateDuration();
        $this->dispatch('chart-updated');
    }

    public function calculateDuration()
    {
        $practitionerId = auth()->user()->practitioner->id ?? null;

        if (! $practitionerId) {
            return;
        }

        $dateRange = $this->getDateRange();

        // Calculate average consultation duration
        $this->calculateAverageDuration($practitionerId, $dateRange);

        // Generate chart data for weekly/monthly views
        if ($this->filter !== 'daily') {
            $this->generateChartData($practitionerId, $dateRange);
        }
    }

    private function getDateRange()
    {
        $now = Carbon::now();

        return match ($this->filter) {
            'daily' => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'weekly' => [
                'start' => $now->copy()->subDays(6)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            'monthly' => [
                'start' => $now->copy()->subDays(29)->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
            default => [
                'start' => $now->copy()->startOfDay(),
                'end' => $now->copy()->endOfDay(),
            ],
        };
    }

    private function calculateAverageDuration($practitionerId, $dateRange)
    {
        $encounters = Encounter::where('practitioner_id', $practitionerId)
            ->whereBetween('start', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('start')
            ->whereNotNull('end')
            ->where('status', 'finished')
            ->get();

        if ($encounters->isEmpty()) {
            $this->averageDuration = 0;

            return;
        }

        $totalMinutes = 0;
        $count = 0;

        foreach ($encounters as $encounter) {
            $startTime = Carbon::parse($encounter->start);
            $endTime = Carbon::parse($encounter->end);
            $diffInMinutes = $startTime->diffInMinutes($endTime);

            $totalMinutes += $diffInMinutes;
            $count++;
        }

        if ($count > 0) {
            $this->averageDuration = round($totalMinutes / $count, 1);
        } else {
            $this->averageDuration = 0;
        }
    }

    private function generateChartData($practitionerId, $dateRange)
    {
        $this->chartData = [];

        if ($this->filter === 'weekly') {
            // Generate data for last 7 days
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $dayStart = $date->copy()->startOfDay();
                $dayEnd = $date->copy()->endOfDay();

                $encounters = Encounter::where('practitioner_id', $practitionerId)
                    ->whereBetween('start', [$dayStart, $dayEnd])
                    ->whereNotNull('start')
                    ->whereNotNull('end')
                    ->where('status', 'finished')
                    ->get();

                $avgMinutes = 0;
                if ($encounters->isNotEmpty()) {
                    $totalMinutes = 0;
                    foreach ($encounters as $encounter) {
                        $startTime = Carbon::parse($encounter->start);
                        $endTime = Carbon::parse($encounter->end);
                        $totalMinutes += $startTime->diffInMinutes($endTime);
                    }
                    $avgMinutes = $totalMinutes / $encounters->count();
                }

                $this->chartData[] = [
                    'label' => $date->format('D d'),
                    'value' => round($avgMinutes, 1),
                ];
            }
        } elseif ($this->filter === 'monthly') {
            // Generate data for last 4 weeks
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();

                $encounters = Encounter::where('practitioner_id', $practitionerId)
                    ->whereBetween('start', [$weekStart, $weekEnd])
                    ->whereNotNull('start')
                    ->whereNotNull('end')
                    ->where('status', 'finished')
                    ->get();

                $avgMinutes = 0;
                if ($encounters->isNotEmpty()) {
                    $totalMinutes = 0;
                    foreach ($encounters as $encounter) {
                        $startTime = Carbon::parse($encounter->start);
                        $endTime = Carbon::parse($encounter->end);
                        $totalMinutes += $startTime->diffInMinutes($endTime);
                    }
                    $avgMinutes = $totalMinutes / $encounters->count();
                }

                $this->chartData[] = [
                    'label' => 'Sem '.$weekStart->format('d/m'),
                    'value' => round($avgMinutes, 1),
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.doctor.consultation-duration-time');
    }
}
