<?php

namespace App\Http\Middleware;

use App\Models\Tournament;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAvailablleTournament
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $check = Tournament::where('status', 'active')->first();

        if (!$check) {
            return redirect()->route('tournament.index')->with('noError', 'No active tournament available. Please activate an existing tournament or create a new tournament.');
        }

        return $next($request);
    }
}
