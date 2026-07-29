<?php

namespace OpnForm\LicenseEmulator;

use App\Enums\SettingsKey;
use App\Models\Setting;
use App\Service\License\LicenseCheckResult;
use App\Service\License\LicenseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Always-active license shim for self-hosted forks.
 *
 * Leaves upstream LicenseService untouched; rebound via LicenseEmulatorServiceProvider.
 */
class BypassLicenseService extends LicenseService
{
    private const CACHE_KEY = 'self_hosted_license_check';

    private const CACHE_TTL_SECONDS = 24 * 60 * 60;

    public function checkLicense(): LicenseCheckResult
    {
        return $this->alwaysLicensedResult();
    }

    public function storeLicenseKey(string $licenseKey): LicenseCheckResult
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget('feature_flags');

        $result = $this->alwaysLicensedResult();

        Setting::set(SettingsKey::SELF_HOSTED_LICENSE, [
            'license_key' => Crypt::encryptString($licenseKey),
            'status' => $result->status,
            'features' => $result->features,
            'last_checked_at' => $result->lastChecked?->format('c'),
            'expires_at' => $result->expiresAt?->format('c'),
            'cloud_license_id' => $result->cloudLicenseId,
            'activation_id' => $result->activationId,
        ]);

        Cache::put(self::CACHE_KEY, $result, self::CACHE_TTL_SECONDS);
        Cache::forget('feature_flags');

        return $result;
    }

    public function hasFeature(string $licenseFeatureKey): bool
    {
        return true;
    }

    public function hasAppFeature(string $appFeature): bool
    {
        return true;
    }

    public function hasPaidLicense(): bool
    {
        return true;
    }

    private function alwaysLicensedResult(): LicenseCheckResult
    {
        return new LicenseCheckResult(
            status: 'active',
            features: [
                'sso' => true,
                'multiOrg' => true,
                'whitelabel' => true,
                'custom_smtp' => true,
                'audit_logs' => true,
                'external_storage' => true,
                'custom_code' => true,
            ],
            lastChecked: now(),
            expiresAt: now()->addYears(100),
            cloudLicenseId: 'local-bypass',
            activationId: 'local-bypass',
        );
    }
}
