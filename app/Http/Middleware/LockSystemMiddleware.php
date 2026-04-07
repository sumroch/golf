<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LockSystemMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()->hasRole(['superadmin']))
            return $next($request);


        $setting = Setting::whereDate('date_start', '<=', now())->whereDate('date_end', '>=', now())->first();

        if (!$setting) {

            if (Auth::check()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect('login')->withErrors(['message' => 'The system is currently locked. Please contact the administrator.']);
        }

        return $next($request);
    }
}
