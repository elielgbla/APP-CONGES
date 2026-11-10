<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Response;

class CheckLocalNetwork
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if (is_string($ip) && IpUtils::checkIp($ip, config('app.local_networks', []))) {
            return $next($request);
        }

        // Si l'accès vient de l'extérieur, on bloque
        abort(403, "Accès refusé. Cette application est uniquement accessible depuis le réseau local de l'entreprise.");
    }
}