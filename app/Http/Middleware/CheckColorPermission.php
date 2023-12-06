<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckColorPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && session('RoleHasPermission')->colors == 1) {
            return $next($request);
        }

        // Redirect to dashboard if the user doesn't have permission to access the Color
        return redirect()->route('admin:dashboard');
    }
}
