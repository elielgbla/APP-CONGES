<?php

namespace App\Http\Controllers;

use App\Models\Journalisation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class JournalisationController extends Controller implements HasMiddleware
{
    /**
     * Définition des middlewares du contrôleur.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = Auth::user();

                // Vérification stricte du rôle/poste de l'utilisateur connecté
                if (!$user || !in_array($user->code_poste, ['ADMIN', 'RH'])) {
                    abort(403, "Action non autorisée : Droits d'accès insuffisants.");
                }

                return $next($request);
            }),
        ];
    }
    /**
     * Affichage de la liste des journaux d'audit.
     */
    public function index(Request $request)
    {
        // On charge la relation 'employe' pour optimiser les performances (Eager Loading)
        $query = Journalisation::with('employe');

        // Recherche par mot-clé (Matricule, Détails, Action)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('matricule_employe', 'LIKE', "%{$search}%")
                  ->orWhere('details', 'LIKE', "%{$search}%")
                  ->orWhere('action', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par type d'action
        if ($request->filled('action_filter')) {
            $query->where('action', $request->input('action_filter'));
        }

        // Filtre par table concernée
        if ($request->filled('table_filter')) {
            $query->where('table_concernee', $request->input('table_filter'));
        }

        // Filtre par plage de dates
        if ($request->filled('date_debut') && $request->filled('date_fin')) {
            $query->whereBetween('date_action', [
                $request->input('date_debut') . ' 00:00:00',
                $request->input('date_fin') . ' 23:59:59',
            ]);
        }

        // Pagination en conservant les filtres actifs dans l'URL (withQueryString)
        $logs = $query->orderBy('date_action', 'desc')->paginate(15)->withQueryString();

        // Récupération des valeurs distinctes pour alimenter les listes déroulantes des filtres
        $distinctActions = Journalisation::distinct()->orderBY('action')->pluck('action');
        $distinctTables = Journalisation::distinct()->orderBy('table_concernee')->pluck('table_concernee');

        return view('journalisation.index', compact('logs', 'distinctActions', 'distinctTables'));
    }

    /**
     * Affichage des détails d'une trace d'audit spécifique.
     */
    public function show(string $code_log)
    {
        // Chargement du log avec son employé lié
        $log = Journalisation::with('employe')->findOrFail($code_log);

        return view('journalisation.show', compact('log'));
    }

    /* |--------------------------------------------------------------------------
    | Blocage de la manipulation des données (Sécurité Audit)
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        abort(405, 'Méthode non autorisée : Les traces d\'audit se génèrent automatiquement.');
    }

    public function store(Request $request)
    {
        abort(405, 'Méthode non autorisée.');
    }

    public function edit(string $id)
    {
        abort(405, 'Sécurité : Un journal d\'audit ne peut pas être modifié.');
    }

    public function update(Request $request, string $id)
    {
        abort(405, 'Sécurité : Modification interdite.');
    }

    public function destroy(string $id)
    {
        abort(405, 'Sécurité : Interdiction stricte de supprimer une trace d\'audit.');
    }
}