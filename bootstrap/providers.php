<?php

use App\Providers\AppServiceProvider;
use App\Providers\BroadcastServiceProvider;
use App\Providers\DashboardServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\RateLimitServiceProvider;

return [
    AppServiceProvider::class,
    BroadcastServiceProvider::class,
    DashboardServiceProvider::class,
    FortifyServiceProvider::class,
    RateLimitServiceProvider::class,
];
