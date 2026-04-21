<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserPresence
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $request->user()->update([
                'last_seen_at' => now(),
                'online_status' => 'online',
            ]);
        }

        return $next($request);
    }
}