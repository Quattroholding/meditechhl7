<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SelectInput extends Component
{
    public string $name;

    public array $options;

    public ?array $selected;

    public function __construct(string $name, array $options = [], $selected = null)
    {
        $this->name = $name;
        $this->options = $options;
        // Convert single value to array, null to empty array
        if ($selected === null) {
            $this->selected = [];
        } elseif (is_array($selected)) {
            $this->selected = $selected;
        } else {
            $this->selected = [$selected];
        }
    }

    public function render()
    {
        return view('components.select-input');
    }
}
