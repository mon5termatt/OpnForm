<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AcceptsJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if ($this->preservesClientAccept($request)) {
            return $next($request);
        }

        $request->headers->set('Accept', 'application/json');

        return $next($request);
    }

    private function preservesClientAccept(Request $request): bool
    {
        return $request->is('mcp')
            || $request->is('oauth/authorize')
            || $request->is('oauth/authorize/*');
    }
}
