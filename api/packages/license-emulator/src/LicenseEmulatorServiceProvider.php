<?php

namespace OpnForm\LicenseEmulator;

use App\Service\License\LicenseService;
use App\Service\License\SelfHostedSeatLimitService;
use Illuminate\Support\ServiceProvider;

class LicenseEmulatorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LicenseService::class, BypassLicenseService::class);
        $this->app->singleton(SelfHostedSeatLimitService::class, UnlimitedSeatLimitService::class);
    }
}
