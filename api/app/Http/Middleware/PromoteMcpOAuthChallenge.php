<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PromoteMcpOAuthChallenge
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->is('mcp')) {
            return $response;
        }

        $challenge = $this->challengeFrom($response);
        if ($challenge === null) {
            return $response;
        }

        $response->setStatusCode(401);
        $response->headers->set('WWW-Authenticate', $challenge);

        return $response;
    }

    private function challengeFrom(Response $response): ?string
    {
        $status = $response->getStatusCode();
        if ($status !== 200 && $status !== 401) {
            return null;
        }

        $content = $response->getContent();
        if (! is_string($content) || $content === '') {
            return null;
        }

        $payload = json_decode($content, true);
        if (! is_array($payload)) {
            return null;
        }

        $challenge = $payload['result']['_meta']['mcp/www_authenticate'][0]
            ?? $payload['error']['_meta']['mcp/www_authenticate'][0]
            ?? null;

        return is_string($challenge) && $challenge !== '' ? $challenge : null;
    }
}
