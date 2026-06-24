<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckAppLifecycle
{
    /**
     * Vérifie si la licence de maintenance de 5 ans est toujours valide.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Récupère la date d'installation depuis le fichier .env (par défaut aujourd'hui si absent)
        $installDateString = env('APP_INSTALLATION_DATE', Carbon::now()->toDateString());
        $installDate = Carbon::parse($installDateString);
        
        // Calcul du cycle de vie (10 ans maximum)
        $expirationDate = $installDate->copy()->addYears(10);

        // Si la date actuelle dépasse la date d'expiration
        if (Carbon::now()->greaterThan($expirationDate)) {
            // Laisse uniquement l'accès à la page de blocage de maintenance
            if (!$request->is('maintenance-licence')) {
                return redirect()->route('maintenance.licence');
            }
        }

        return $next($request);
    }
}