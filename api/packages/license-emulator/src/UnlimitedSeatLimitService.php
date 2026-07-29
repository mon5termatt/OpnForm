<?php

namespace OpnForm\LicenseEmulator;

use App\Models\UserInvite;
use App\Service\License\SelfHostedSeatLimitService;

/**
 * No seat caps — used with the license emulator on self-hosted forks.
 */
class UnlimitedSeatLimitService extends SelfHostedSeatLimitService
{
    public function canCreateUser(string $email): bool
    {
        return true;
    }

    public function canInviteEmail(string $email): bool
    {
        return true;
    }

    public function canAcceptInvite(UserInvite $invite): bool
    {
        return true;
    }
}
