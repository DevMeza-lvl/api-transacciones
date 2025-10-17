<?php

namespace App\Http\Middleware;

use Closure;

class ForceCorsHeaders
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        // Opcionalmente manejar la petición OPTIONS
        if ($request->getMethod() === 'OPTIONS') {
            return response('OK', 200)->withHeaders($response->headers->all());
        }

        return $response;
    }
}
