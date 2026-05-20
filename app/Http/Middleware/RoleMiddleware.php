<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Split the roles by pipe (e.g., "super-admin|center-admin" becomes an array)
        $roles = explode('|', $role);

        // Check if the user's role slug exists in the allowed list
        // Note: We use strict comparison matching your debug output "super-admin"
        if (! in_array($request->user()->role->slug, $roles)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}