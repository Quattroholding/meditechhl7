<?php

namespace App\Livewire;

use Livewire\Component;

class WelcomeSalute extends Component
{
    public $userName;
    public $greetingMessage;
    public $welcomeMessage;
    public $backgroundImage;
    public $duration;

    public function mount(
        $userName = null,
        $greetingMessage = null,
        $welcomeMessage = null,
        $backgroundImage = null,
        $duration = 8000
    ) {
        $this->userName = $userName ?? auth()->user()->full_name ?? 'Usuario';
        $this->greetingMessage = $greetingMessage ?? __('generic.hello');
        $this->welcomeMessage = $welcomeMessage ?? __('generic.welcome');
        $this->backgroundImage = $backgroundImage ?? asset('/assets/img/banner2.png');
        $this->duration = $duration;

    }

    public function render()
    {
        return view('livewire.welcome-salute');
    }
}
