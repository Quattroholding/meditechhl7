<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AccordionItem extends Component
{
    public $title;
    public $isOpen;

    public function __construct($title, $isOpen = false)
    {
        $this->title = $title;
        $this->isOpen = $isOpen;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.accordion-item');
    }
}
