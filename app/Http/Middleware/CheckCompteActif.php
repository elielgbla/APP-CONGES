<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCompteActif
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Si l'utilisateur est connecté et que son statut est "inactif"
        if (Auth::check() && (strtoupper(Auth::user()->statut_compte) === 'INACTIF' || strtoupper(Auth::user()->statut_compte) === 'SUSPENDU')) {

            // On laisse l'accès uniquement à la page de statut ou à la déconnexion pour éviter une boucle infinie
            if (!$request->is('compte-inactif') && !$request->is('logout')) {
                return redirect()->route('compte.inactif');
            }
        }

        return $next($request);
    }
}
