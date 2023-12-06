<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckNTReportPermission
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
        if (auth()->check() && session('RoleReportsPermission') !== null && session('RoleReportsPermission')->nt_summary == 1) {
            return $next($request);
        }

        // Redirect to dashboard if the user doesn't have permission to access the CGT Report
        return redirect()->route('admin:dashboard');
    }
}
