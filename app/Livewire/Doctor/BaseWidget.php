<?php

namespace App\Livewire\Doctor;

use Livewire\Component;

abstract class BaseWidget extends Component
{
    public $order;

    public $isLoading = true;

    public function mount()
    {
        // Los widgets no cargan datos inmediatamente
        // Esperan la llamada explícita a loadData()
    }

    public function loadData()
    {
        // Método que debe ser implementado por cada widget
        $this->loadWidgetData();
        $this->isLoading = false;
    }

    // Método abstracto que debe ser implementado por cada widget
    abstract protected function loadWidgetData();

    // Método helper para crear skeleton loading
    protected function renderSkeleton($rows = 3, $showChart = false)
    {
        $skeletonView = view('components.widget-skeleton', [
            'rows' => $rows,
            'showChart' => $showChart,
        ])->render();

        return $skeletonView;
    }
}
