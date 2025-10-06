<?php

namespace App\Livewire;

use App\Models\UserWidgetPreference;
use Livewire\Component;

class WidgetConfiguration extends Component
{
    public $dashboardType;

    public $widgets = [];

    public $showModal = false;

    public $hasChanges = false;

    public function mount($dashboardType = 'doctor')
    {
        $this->dashboardType = $dashboardType;
        $this->loadWidgets();
    }

    public function loadWidgets()
    {
        $defaultWidgets = UserWidgetPreference::getDefaultWidgets($this->dashboardType);
        $userPreferences = UserWidgetPreference::where('user_id', auth()->id())
            ->where('dashboard_type', $this->dashboardType)
            ->get()
            ->keyBy('widget_name');

        $this->widgets = collect($defaultWidgets)->map(function ($widget, $key) use ($userPreferences) {
            $preference = $userPreferences->get($key);

            return [
                'key' => $key,
                'name' => $widget['name'],
                'description' => $preference ? $preference->widget_description : $widget['description'],
                'is_visible' => $preference ? $preference->is_visible : true,
                'order_position' => $preference ? $preference->order_position : $widget['order'],
                'width' => $preference ? $preference->width : $widget['width'],
            ];
        })->sortBy('order_position')->values()->toArray();
    }

    public function toggleWidget($widgetKey)
    {
        $widget = collect($this->widgets)->firstWhere('key', $widgetKey);
        if (! $widget) {
            return;
        }

        $widget['is_visible'] = ! $widget['is_visible'];

        // Update in widgets array
        $this->widgets = collect($this->widgets)->map(function ($w) use ($widgetKey, $widget) {
            return $w['key'] === $widgetKey ? $widget : $w;
        })->toArray();

        $this->savePreference($widgetKey, $widget['is_visible'], $widget['order_position'], $widget['description'], $widget['width']);
        $this->hasChanges = true;
    }

    public function changeWidgetWidth($widgetKey, $width)
    {
        $widget = collect($this->widgets)->firstWhere('key', $widgetKey);
        if (! $widget) {
            return;
        }

        $widget['width'] = $width;

        // Update in widgets array
        $this->widgets = collect($this->widgets)->map(function ($w) use ($widgetKey, $widget) {
            return $w['key'] === $widgetKey ? $widget : $w;
        })->toArray();

        $this->savePreference($widgetKey, $widget['is_visible'], $widget['order_position'], $widget['description'], $widget['width']);
        $this->hasChanges = true;
    }

    public function updateOrder($orderedWidgets)
    {
        // Update the widgets array with new order
        $this->widgets = collect($orderedWidgets)->map(function ($widget, $index) {
            $existingWidget = collect($this->widgets)->firstWhere('key', $widget['key']);

            return [
                'key' => $widget['key'],
                'name' => $existingWidget['name'],
                'description' => $existingWidget['description'],
                'is_visible' => $widget['is_visible'],
                'order_position' => $widget['order_position'],
                'width' => $existingWidget['width'] ?? 'col-lg-6',
            ];
        })->toArray();

        // Save preferences to database
        foreach ($orderedWidgets as $widget) {
            $existingWidget = collect($this->widgets)->firstWhere('key', $widget['key']);
            $this->savePreference($widget['key'], $widget['is_visible'], $widget['order_position'], $widget['description'], $existingWidget['width'] ?? 'col-lg-6');
        }

        $this->hasChanges = true;
    }

    private function savePreference($widgetKey, $isVisible, $orderPosition, $description, $width = 'col-lg-6')
    {
        UserWidgetPreference::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'dashboard_type' => $this->dashboardType,
                'widget_name' => $widgetKey,
            ],
            [
                'is_visible' => $isVisible,
                'order_position' => $orderPosition,
                'width' => $width,
                'widget_description' => $description,
            ]
        );
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;

        // Dispatch event to reload the dashboard only if there were changes
        if ($this->hasChanges) {
            $this->dispatch('reload-dashboard');
        }
    }

    public function render()
    {
        return view('livewire.widget-configuration');
    }
}
