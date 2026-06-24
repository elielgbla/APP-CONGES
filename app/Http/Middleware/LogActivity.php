<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Journalisation;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next, string $action = 'Navigation', string $table = null): Response
    {
        $response = $next($request);

        // On n'enregistre que si l'utilisateur est connecté ou vient de tenter une action importante
        if (Auth::check() || $request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('DELETE')) {
            
            // Détermination dynamique de l'action si non spécifiée
            $finalAction = $action;
            if ($action === 'Navigation' && $request->isMethod('POST')) {
                $finalAction = 'Soumission de données';
            }

            Journalisation::create([
                'matricule_employe' => Auth::check() ? Auth::user()->matricule_employe : null,
                'action' => $finalAction,
                'table_concernee' => $table ?? $this->guessTable($request),
                'details' => json_encode([
                    'url' => $request->fullUrl(),
                    'method' => $request->method(),
                    'input' => $request->except(['mot_de_passe', 'mot_de_passe_confirmation', '_token']),
                ], JSON_UNESCAPED_UNICODE),
                'ip_address' => $request->ip(),
            ]);
        }

        return $response;
    }

    private function guessTable(Request $request): ?string
    {
        if ($request->is('*/demandes*')) return 'demande';
        if ($request->is('*/employes*')) return 'employe';
        if ($request->is('*/validations*')) return 'validation_permission';
        return null;
    }
}