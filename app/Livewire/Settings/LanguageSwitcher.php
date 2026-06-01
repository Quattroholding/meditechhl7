<?php

namespace App\Livewire\Settings;

use App\Models\ClientPreference;
use Illuminate\Support\Facades\App;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public $currentLocale;

    public $availableLocales = [
        'es' => 'Español',
        'en' => 'English',
    ];

    public function mount(): void
    {
        $this->currentLocale = App::getLocale();
    }

    public function switchLanguage($locale): void
    {
        if (! in_array($locale, array_keys($this->availableLocales))) {
            return;
        }

        $client = auth()->user()->getCurrentClient();

        if (! $client) {
            session()->flash('error', __('generic.no_client_selected'));
            $this->dispatch('language-update-failed');

            return;
        }

        try {
            // Update client preference
            ClientPreference::setLanguage($client->id, $locale);

            // Clear session cache
            session()->forget('locale_client_'.$client->id);

            // Set new locale immediately
            App::setLocale($locale);

            // Update current locale in component
            $this->currentLocale = $locale;

            session()->flash('success', __('settings.language_updated'));

            // Dispatch event to reload page
            $this->dispatch('language-changed');
        } catch (\Exception $e) {
            session()->flash('error', __('settings.language_update_failed'));
            $this->dispatch('language-update-failed');
        }
    }

    public function render()
    {
        return view('livewire.settings.language-switcher');
    }
}
