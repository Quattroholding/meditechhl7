<?php

namespace App\Livewire;

use App\Models\UserWidgetPreference;
use Illuminate\Support\Facades\Log;
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
            $width = $preference ? $preference->width : $widget['width'];
            $order = $preference ? $preference->order_position : $widget['order'];
            $height = $preference ? ($preference->height ?? 1) : ($widget['height'] ?? 1);

            // Prioridad de posiciones:
            // 1. Posición guardada en preferencias del usuario
            // 2. Posición definida en default widgets
            // 3. Generar posición automáticamente
            $position = $preference && $preference->position
                ? $preference->position
                : ($widget['position'] ?? UserWidgetPreference::generateSpatiePosition($order, $width, $height));

            return [
                'key' => $key,
                'name' => $widget['name'],
                'description' => $preference ? $preference->widget_description : $widget['description'],
                'is_visible' => $preference ? $preference->is_visible : true,
                'order_position' => $order,
                'width' => $width,
                'position' => $position,
                'height' => $height,
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
        // Recalcular posición con el nuevo ancho
        $widget['position'] = UserWidgetPreference::generateSpatiePosition(
            $widget['order_position'],
            $width,
            $widget['height'] ?? 1
        );

        // Update in widgets array
        $this->widgets = collect($this->widgets)->map(function ($w) use ($widgetKey, $widget) {
            return $w['key'] === $widgetKey ? $widget : $w;
        })->toArray();

        $this->savePreference(
            $widgetKey,
            $widget['is_visible'],
            $widget['order_position'],
            $widget['description'],
            $widget['width'],
            $widget['position'],
            $widget['height'] ?? 1
        );
        $this->hasChanges = true;
    }

    public function changeWidgetHeight($widgetKey, $height)
    {
        $widget = collect($this->widgets)->firstWhere('key', $widgetKey);
        if (! $widget) {
            return;
        }

        $widget['height'] = $height;
        // Recalcular posición con la nueva altura
        $widget['position'] = UserWidgetPreference::generateSpatiePosition(
            $widget['order_position'],
            $widget['width'],
            $height
        );

        // Update in widgets array
        $this->widgets = collect($this->widgets)->map(function ($w) use ($widgetKey, $widget) {
            return $w['key'] === $widgetKey ? $widget : $w;
        })->toArray();

        $this->savePreference(
            $widgetKey,
            $widget['is_visible'],
            $widget['order_position'],
            $widget['description'],
            $widget['width'],
            $widget['position'],
            $height
        );
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

    private function savePreference($widgetKey, $isVisible, $orderPosition, $description, $width = 'col-lg-6', $position = null, $height = 1)
    {
        // Si no se proporciona posición, generarla
        if (! $position) {
            $position = UserWidgetPreference::generateSpatiePosition($orderPosition, $width, $height);
        }

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
                'position' => $position,
                'height' => $height,
                'widget_description' => $description,
            ]
        );
    }

    public function openModal()
    {
        $this->showModal = true;
    }

    public function placeWidgetOnGrid($widgetKey, $position)
    {
        Log::info('placeWidgetOnGrid called', [
            'widget_key' => $widgetKey,
            'position' => $position,
        ]);

        // Find the widget
        $widgetIndex = collect($this->widgets)->search(function ($w) use ($widgetKey) {
            return $w['key'] === $widgetKey;
        });

        if ($widgetIndex === false) {
            Log::error('Widget not found', ['widget_key' => $widgetKey]);

            return;
        }

        // Update widget
        $this->widgets[$widgetIndex]['is_visible'] = true;
        $this->widgets[$widgetIndex]['position'] = $position;

        // Parse position to determine width and height
        [$start, $end] = explode(':', $position);
        $startCol = ord($start[0]) - 96; // a=1, b=2, etc.
        $endCol = ord($end[0]) - 96;
        $startRow = (int) substr($start, 1);
        $endRow = (int) substr($end, 1);

        $colsWidth = $endCol - $startCol + 1;
        $height = $endRow - $startRow + 1;

        // Convert cols to width
        $width = match ($colsWidth) {
            3 => 'col-lg-3',
            4 => 'col-lg-4',
            6 => 'col-lg-6',
            9 => 'col-lg-9',
            12 => 'col-lg-12',
            default => 'col-lg-6'
        };

        $this->widgets[$widgetIndex]['width'] = $width;
        $this->widgets[$widgetIndex]['height'] = $height;

        // Save to database
        $this->savePreference(
            $widgetKey,
            true,
            $this->widgets[$widgetIndex]['order_position'],
            $this->widgets[$widgetIndex]['description'],
            $width,
            $position,
            $height
        );

        $this->hasChanges = true;
        $this->loadWidgets(); // Reload to update UI
    }

    public function removeWidgetFromGrid($widgetKey)
    {
        Log::info('removeWidgetFromGrid called', ['widget_key' => $widgetKey]);

        // Find the widget
        $widgetIndex = collect($this->widgets)->search(function ($w) use ($widgetKey) {
            return $w['key'] === $widgetKey;
        });

        if ($widgetIndex === false) {
            return;
        }

        // Update widget
        $this->widgets[$widgetIndex]['is_visible'] = false;

        // Save to database
        $this->savePreference(
            $widgetKey,
            false,
            $this->widgets[$widgetIndex]['order_position'],
            $this->widgets[$widgetIndex]['description'],
            $this->widgets[$widgetIndex]['width'],
            $this->widgets[$widgetIndex]['position'] ?? null,
            $this->widgets[$widgetIndex]['height'] ?? 1
        );

        $this->hasChanges = true;
        $this->loadWidgets(); // Reload to update UI
    }

    public function updateWidgetPosition($widgetKey, $newPosition)
    {
        Log::info('updateWidgetPosition called', [
            'widget_key' => $widgetKey,
            'position' => $newPosition,
        ]);

        // Similar logic to placeWidgetOnGrid
        $this->placeWidgetOnGrid($widgetKey, $newPosition);
    }

    public function resetToDefaults()
    {
        Log::info('resetToDefaults called', [
            'user_id' => auth()->id(),
            'dashboard_type' => $this->dashboardType,
        ]);

        // Eliminar todas las preferencias del usuario para este dashboard
        $deleted = UserWidgetPreference::where('user_id', auth()->id())
            ->where('dashboard_type', $this->dashboardType)
            ->delete();

        Log::info('Preferences deleted', ['count' => $deleted]);

        // Recargar los widgets con la configuración por defecto
        $this->loadWidgets();
        $this->hasChanges = true;

        Log::info('Widgets reloaded', ['count' => count($this->widgets)]);

        // Mensaje de confirmación (opcional)
        session()->flash('message', 'Configuración restablecida a valores por defecto.');
    }

    public function closeModal()
    {
        $this->showModal = false;

        // Dispatch event to reload the page if there were changes
        if ($this->hasChanges) {
            $this->dispatch('reload-page');
        }
    }

    public function render()
    {
        // Use v2 if available, otherwise fallback to v1
        if (view()->exists('livewire.widget-configuration-v2')) {
            return view('livewire.widget-configuration-v2');
        }

        return view('livewire.widget-configuration');
    }
}
