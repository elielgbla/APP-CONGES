<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;

class CheckAppLifecycle
{
    /**
     * Vérifie si la licence de maintenance de 5 ans est toujours valide.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $installDateString = config('app.installation_date');
        $lifecycleYears = config('app.lifecycle_years');

        if (!is_string($installDateString) || $installDateString === '' || !is_int($lifecycleYears) || $lifecycleYears < 1) {
            throw new InvalidArgumentException('La date d’installation et la durée de vie de l’application doivent être configurées.');
        }

        $installDate = Carbon::createFromFormat('!Y-m-d', $installDateString);

        if (!$installDate || $installDate->format('Y-m-d') !== $installDateString) {
            throw new InvalidArgumentException('APP_INSTALLATION_DATE doit respecter le format AAAA-MM-JJ.');
        }

        $expirationDate = $installDate->copy()->addYearsNoOverflow($lifecycleYears);

        if (Carbon::today()->greaterThan($expirationDate)) {
            // Laisse uniquement l'accès à la page de blocage de maintenance
            if (!$request->is('maintenance-licence')) {
                return redirect()->route('maintenance.licence');
            }
        }

        return $next($request);
    }
}