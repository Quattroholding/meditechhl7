<?php
// app/View/Components/Offcanvas.php

namespace App\View\Components;

use Illuminate\View\Component;

class Offcanvas extends Component
{
    public $id;
    public $title;
    public $position;
    public $backdrop;
    public $scroll;
    public $size;

    public function __construct(
        $id,
        $title = '',
        $position = 'right',
        $backdrop = true,
        $scroll = false,
        $size = 'md'
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->position = in_array($position, ['top', 'right', 'bottom', 'left']) ? $position : 'right';
        $this->backdrop = $backdrop;
        $this->scroll = $scroll;
        $this->size = in_array($size, ['sm', 'md', 'lg', 'xl']) ? $size : 'md';
    }

    public function render()
    {
        return view('components.offcanvas');
    }

    public function getPositionClasses()
    {
        $classes = [
            'top' => 'offcanvas-top',
            'right' => 'offcanvas-end',
            'bottom' => 'offcanvas-bottom',
            'left' => 'offcanvas-start'
        ];

        return $classes[$this->position] ?? 'offcanvas-end';
    }

    public function getSizeClasses()
    {
        if (in_array($this->position, ['top', 'bottom'])) {
            $sizes = [
                'sm' => 'offcanvas-size-sm-vertical',
                'md' => 'offcanvas-size-md-vertical',
                'lg' => 'offcanvas-size-lg-vertical',
                'xl' => 'offcanvas-size-xl-vertical'
            ];
        } else {
            $sizes = [
                'sm' => 'offcanvas-size-sm',
                'md' => 'offcanvas-size-md',
                'lg' => 'offcanvas-size-lg',
                'xl' => 'offcanvas-size-xl'
            ];
        }

        return $sizes[$this->size] ?? '';
    }
}
