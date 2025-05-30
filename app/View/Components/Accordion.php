<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Accordion extends Component
{

    public $allowMultiple;

    /**
     * Create a new component instance.
     */
    public function __construct($allowMultiple = true)
    {
        $this->allowMultiple = $allowMultiple;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.accordion');
    }
}
