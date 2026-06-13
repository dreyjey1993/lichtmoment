<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    public function handle(Request $request, Closure $next)
    {
        Auth::shouldUse('web');

        if (Auth::check()) {
            return $next($request);
        }

        if (session()->has('admin_id')) {
            return $next($request);
        }

        return redirect()->route('admin.login');
    }
}
