# OpnForm License Emulator

Self-hosted add-on that rebinds Laravel’s `LicenseService` so all license checks
return an active enterprise license (`local-bypass` markers), and rebinds
`SelfHostedSeatLimitService` so the free 2-user seat cap is not enforced.

## Why this exists

Upstream `App\Service\License\LicenseService` stays unmodified so merges from
OpnForm stay clean. Unlock logic lives only in this path package.

## Apply

1. Copy `api/packages/license-emulator/` into the tree.
2. In `api/composer.json`, add a path repository and require:

```json
"repositories": [
  { "type": "path", "url": "packages/license-emulator" }
],
"require": {
  "opnform/license-emulator": "*"
}
```

3. Run `composer update opnform/license-emulator` in `api/`.

Laravel package auto-discovery registers `LicenseEmulatorServiceProvider`.

## Remove

Drop the require (and repository), run `composer update`, delete this folder.

## Scope

Backend unlock only. Not for production SaaS clouds that rely on real licenses.
