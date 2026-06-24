<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckLocalNetwork
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // Autoriser localhost et les plages IP privées locales (Ex: Abidjan LAN)
        $allowedPrefixes = ['127.0.0.1', '::1', '192.168.', '10.', '172.16.'];

        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($ip, $prefix)) {
                return $next($request);
            }
        }

        // Si l'accès vient de l'extérieur, on bloque
        abort(403, "Accès refusé. Cette application est uniquement accessible depuis le réseau local de l'entreprise.");
    }
}