<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Restrict /admin/* routes to authenticated staff accounts.
     * Both 'admin' and 'editor' roles are allowed in; finer-grained
     * permission checks (e.g. who can delete) are added per-controller later.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! in_array($request->user()->role, ['admin', 'editor'])) {
            abort(403, 'You do not have access to the admin area.');
        }

        return $next($request);
    }
}