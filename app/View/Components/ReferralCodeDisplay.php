<?php

namespace App\View\Components;

use App\Models\Client;
use App\Models\ReferralCode;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReferralCodeDisplay extends Component
{
    public ?ReferralCode $referralCode;

    public string $shareUrl;

    /**
     * Create a new component instance.
     */
    public function __construct(public Client $client)
    {
        // Get or create referral code for this client
        $this->referralCode = $client->referralCode;

        if (! $this->referralCode) {
            $referralService = app(\App\Services\ReferralService::class);
            $this->referralCode = $referralService->generateCode($client);
        }

        // Generate shareable registration URL with referral code
        $this->shareUrl = route('public.register', ['ref' => $this->referralCode->code]);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.referral-code-display');
    }
}
