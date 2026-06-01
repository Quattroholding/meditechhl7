<?php

namespace App\Livewire\Doctor;

use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class AppointmentLeadTime extends Component
{
    public $loading = true;

    public $filter = 'daily'; // daily, weekly, monthly

    public $averageLeadTime = 0;

    public $chartData = [];

    public $unit = 'min'; // min, hrs, days

    public function mount()
    {
        $this->loading = true;
    }

    public function loadData()
    {
        $this->loading = false;
        $this->calculateLeadTime();
    }

    public function updatedFilter()
    {
        $this->calculateLeadTime();
        $this->dispatch('chart-updated');
    }

    public function calculateLeadTime()
    {
        $practitionerId = auth()->user()->practitioner->id ?? null;

        if (! $practitionerId) {
            return;
        }

        $dateRange = $this->getDateRange();

        // Calculate average lead time
        $this->calculateAverageLeadTime($practitionerId, $dateRange);

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

    private function calculateAverageLeadTime($practitionerId, $dateRange)
    {

        $appointments = Appointment::where('practitioner_id', $practitionerId)
            ->whereBetween('start', [$dateRange['start'], $dateRange['end']])
            ->whereNotNull('start')
            ->whereNotNull('created_at')
            ->whereIn('status', ['fulfilled', 'booked', 'arrived'])
            ->get();

        if ($appointments->isEmpty()) {
            $this->averageLeadTime = 0;
            $this->unit = 'min';

            return;
        }

        $totalMinutes = 0;
        $count = 0;

        foreach ($appointments as $appointment) {
            // Ensure Carbon instances (parse if string, use directly if already Carbon)
            $requestTime = Carbon::parse($appointment->getRawOriginal('created_at'));
            $attentionTime = Carbon::parse($appointment->start);
            $diffInMinutes = $requestTime->diffInMinutes($attentionTime);
            $totalMinutes += $diffInMinutes;
            $count++;
        }

        // dd($totalMinutes);

        if ($count > 0) {
            $avgMinutes = $totalMinutes / $count;

            // Determine appropriate unit and convert
            if ($this->filter === 'daily') {
                // For daily, show in minutes
                $this->averageLeadTime = round($avgMinutes, 1);
                $this->unit = 'min';
            } else {
                // For weekly/monthly, show in hours or days depending on magnitude
                $avgHours = $avgMinutes / 60;

                if ($avgHours >= 48) {
                    // Show in days if more than 48 hours
                    $this->averageLeadTime = round($avgHours / 24, 1);
                    $this->unit = 'días';
                } else {
                    // Show in hours
                    $this->averageLeadTime = round($avgHours, 1);
                    $this->unit = 'hrs';
                }
            }
        } else {
            $this->averageLeadTime = 0;
            $this->unit = 'min';
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

                $appointments = Appointment::where('practitioner_id', $practitionerId)
                    ->whereBetween('start', [$dayStart, $dayEnd])
                    ->whereNotNull('start')
                    ->whereNotNull('created_at')
                    ->whereIn('status', ['fulfilled', 'booked', 'arrived'])
                    ->get();

                $avgMinutes = 0;
                if ($appointments->isNotEmpty()) {
                    $totalMinutes = 0;
                    foreach ($appointments as $appointment) {
                        $requestTime = Carbon::parse($appointment->getRawOriginal('created_at'));
                        $attentionTime = Carbon::parse($appointment->start);
                        $totalMinutes += $requestTime->diffInMinutes($attentionTime);
                    }
                    $avgMinutes = $totalMinutes / $appointments->count();
                }

                $this->chartData[] = [
                    'label' => $date->format('D d'),
                    'value' => round($avgMinutes / 60, 1), // Convert to hours for chart
                ];
            }
        } elseif ($this->filter === 'monthly') {
            // Generate data for last 4 weeks
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
                $weekEnd = Carbon::now()->subWeeks($i)->endOfWeek();

                $appointments = Appointment::where('practitioner_id', $practitionerId)
                    ->whereBetween('start', [$weekStart, $weekEnd])
                    ->whereNotNull('start')
                    ->whereNotNull('created_at')
                    ->whereIn('status', ['fulfilled', 'booked', 'arrived'])
                    ->get();

                $avgMinutes = 0;
                if ($appointments->isNotEmpty()) {
                    $totalMinutes = 0;
                    foreach ($appointments as $appointment) {
                        $requestTime = Carbon::parse($appointment->getRawOriginal('created_at'));
                        $attentionTime = Carbon::parse($appointment->start);
                        $totalMinutes += $requestTime->diffInMinutes($attentionTime);
                    }
                    $avgMinutes = $totalMinutes / $appointments->count();
                }

                $this->chartData[] = [
                    'label' => 'Sem '.$weekStart->format('d/m'),
                    'value' => round($avgMinutes / 60, 1), // Convert to hours for chart
                ];
            }
        }
    }

    public function render()
    {
        return view('livewire.doctor.appointment-lead-time');
    }
}
