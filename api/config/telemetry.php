<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Anonymous Telemetry Enabled
    |--------------------------------------------------------------------------
    |
    | Upstream OpnForm can collect anonymous usage data. This fork defaults
    | telemetry to OFF. Set OPNFORM_ANONYMOUS_TELEMETRY_DISABLED=false if you
    | explicitly want to opt in.
    |
    | When enabled, telemetry only runs in production + self-hosted mode
    | (see TelemetryService::shouldSendTelemetry()).
    |
    | What would be collected if enabled:
    | - Basic usage metrics (form creation, submissions, workspace creation, user additions)
    | - Anonymous instance identifier (UUID)
    | - No PII, form content, submission data, or user emails
    |
    */

    'enabled' => !filter_var(
        env('OPNFORM_ANONYMOUS_TELEMETRY_DISABLED', true),
        FILTER_VALIDATE_BOOLEAN
    ),

    /*
    |--------------------------------------------------------------------------
    | Telemetry Endpoint
    |--------------------------------------------------------------------------
    |
    | The OpenPanel endpoint URL where telemetry events will be sent.
    | This is hardcoded for OpnForm's telemetry collection.
    |
    */

    'endpoint' => 'https://telemetry.opnform.com/track',

    /*
    |--------------------------------------------------------------------------
    | OpenPanel Client ID
    |--------------------------------------------------------------------------
    |
    | OpenPanel client ID for OpnForm telemetry authentication.
    | Hardcoded - users do not need to configure this.
    |
    */

    'client_id' => 'd6d85ed5-723f-48de-894e-db2de1a0c7c1', // NOSONAR - This is a public client ID for telemetry

    /*
    |--------------------------------------------------------------------------
    | OpenPanel Client Secret
    |--------------------------------------------------------------------------
    |
    | OpenPanel client secret for OpnForm telemetry authentication.
    | Hardcoded - users do not need to configure this.
    |
    */

    'client_secret' => 'sec_236ae00cc86099409ee2', // NOSONAR - This is a public client secret for telemetry
];
