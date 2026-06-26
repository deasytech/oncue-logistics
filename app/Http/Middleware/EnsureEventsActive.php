<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEventsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->customer) {
            $hasInactiveEvents = $user->customer->events()
                ->where('is_active', false)
                ->exists();

            if ($hasInactiveEvents) {
                return redirect()->route('guests.list');
            }
        }

        return $next($request);
    }
}
