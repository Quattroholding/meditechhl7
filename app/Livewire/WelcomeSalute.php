<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Http\Request;

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

        // Usar el helper request() para acceder al Request actual
        $request = request();


        // Mostrar el saludo solo si es la primera visita y el parámetro show_salute=true está presente
        if ($request->has('show_salute') && !session()->has('dashboard_visited')) {
            $this->showSalute = true;
            session()->put('dashboard_visited', true);
        }
    }

    public function render()
    {
        return view('livewire.welcome-salute');
    }
}
