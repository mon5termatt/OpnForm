<?php

use App\Service\License\LicenseService;
use OpnForm\LicenseEmulator\BypassLicenseService;

it('rebinds LicenseService to the license emulator bypass', function () {
    $service = app(LicenseService::class);

    expect($service)->toBeInstanceOf(BypassLicenseService::class);

    $result = $service->checkLicense();

    expect($result->status)->toBe('active')
        ->and($result->isActive())->toBeTrue()
        ->and($result->cloudLicenseId)->toBe('local-bypass')
        ->and($result->activationId)->toBe('local-bypass')
        ->and($service->hasFeature('sso'))->toBeTrue()
        ->and($service->hasAppFeature('custom_smtp'))->toBeTrue()
        ->and($service->hasPaidLicense())->toBeTrue();
});

it('rebinds SelfHostedSeatLimitService to remove the free 2-user cap', function () {
    $seats = app(\App\Service\License\SelfHostedSeatLimitService::class);

    expect($seats)->toBeInstanceOf(\OpnForm\LicenseEmulator\UnlimitedSeatLimitService::class)
        ->and($seats->canCreateUser('someone@example.com'))->toBeTrue()
        ->and($seats->canInviteEmail('another@example.com'))->toBeTrue();
});
