<?php

namespace App\Http\Controllers;

use App\Models\HistoriquePoste;
use App\Models\Employe;
use App\Models\Poste;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class HistoriquePosteController extends Controller implements HasMiddleware
{
    /**
     * Définition des middlewares du contrôleur (Accès restreint).
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = Auth::user();

                if (!$user || !in_array($user->code_poste, ['ADMIN', 'RH'])) {
                    abort(403, "Action non autorisée : Droits d'accès insuffisants.");
                }

                return $next($request);
            }),
        ];
    }

    /**
     * INDEX : Liste des Employés avec leur affectation ACTUELLE
     */
    public function index(Request $request): View
    {

        $search = $request->input('search');

        // On part du modèle Employe pour grouper naturellement la liste par agent
        $query = Employe::query();

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('matricule_employe', 'LIKE', "%{$search}%");
            });
        }

        // Utilisation exacte de historiquePostes (avec un "s") déclarée dans ton modèle
        $employes = $query->with(['historiquePostes' => function ($q) {
            $q->whereNull('date_fin')->with(['poste', 'service']);
        }])->orderBy('nom', 'asc')->paginate(15)->withQueryString();

        return view('historique_postes.index', compact('employes', 'search'));
    }

    /**
     * CREATE : Formulaire d'affectation d'une nouvelle mutation
     */
    public function create(): View
    {
        $employes = Employe::orderBy('nom', 'asc')->get();
        $postes = Poste::orderBy('libelle_poste', 'asc')->get();
        $services = Service::orderBy('libelle_service', 'asc')->get();

        return view('historique_postes.create', compact('employes', 'postes', 'services'));
    }

    /**
     * STORE : Enregistrement d'une mutation (avec clôture automatique de la précédente)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'matricule_employe' => 'required|string|exists:employe,matricule_employe',
            'code_poste' => 'required|string|exists:poste,code_poste',
            'code_service' => 'required|string|exists:service,code_service',
            'date_debut' => 'required|date'
        ]);

        $matricule = $request->input('matricule_employe');
        $newDateDebut = $request->input('date_debut');

        DB::transaction(function () use ($matricule, $newDateDebut, $request) {
            // 1. Clôture de l'ancien poste actif (Correction du nom du modèle : HistoriquePoste)
            $posteActuel = HistoriquePoste::where('matricule_employe', '=', $matricule)
                ->whereNull('date_fin')
                ->first();

            if ($posteActuel) {
                $posteActuel->update([
                    'date_fin' => $newDateDebut
                ]);
            }

            // 2. Extraction numérique du code le plus élevé (HISXXXXX)
            $maxNumericCode = HistoriquePoste::select(
                DB::raw("MAX(CAST(SUBSTRING(code_historique, 4) AS UNSIGNED)) as max_val")
            )->value('max_val');

            $nextNumber = $maxNumericCode ? intval($maxNumericCode) + 1 : 1;
            $newCodeHistorique = 'HIS' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // 3. Création de la nouvelle ligne d'historique
            HistoriquePoste::create([
                'code_historique' => $newCodeHistorique,
                'matricule_employe' => $matricule,
                'code_poste' => $request->input('code_poste'),
                'code_service' => $request->input('code_service'),
                'date_debut' => $newDateDebut,
                'date_fin' => null 
            ]);

            // 4. Mise à jour de la table employe
            $employe = Employe::where('matricule_employe', '=', $matricule)->first();
            if ($employe) {
                $employe->update([
                    'code_poste' => $request->input('code_poste'),
                    'code_service' => $request->input('code_service')
                ]);
            }
        });

        return redirect()->route('historique_postes.index')->with('success', 'Mutation enregistrée avec succès.');
    }

    /**
     * SHOW : Tableau de TOUTES les mutations d'un employé spécifique
     */
    public function show(string $matricule_employe): View
    {
        $employe = Employe::where('matricule_employe', $matricule_employe)->firstOrFail();
        
        // Récupération avec le nom du modèle HistoriquePoste corrigé
        $mutations = HistoriquePoste::with(['poste', 'service'])
            ->where('matricule_employe', $matricule_employe)
            ->orderBy('date_debut', 'desc')
            ->get();

        return view('historique_postes.show', compact('employe', 'mutations'));
    }

    /**
     * EDIT : Modification d'une ligne de mutation précise
     */
    public function edit(string $code_historique): View
    {
        $historique = HistoriquePoste::with(['employe', 'poste', 'service'])->findOrFail($code_historique);
        
        $postes = Poste::orderBy('libelle_poste', 'asc')->get();
        $services = Service::orderBy('libelle_service', 'asc')->get();

        return view('historique_postes.edit', compact('historique', 'postes', 'services'));
    }

    /**
     * UPDATE : Sauvegarde des modifications d'une ligne de mutation
     */
    public function update(Request $request, string $code_historique): RedirectResponse
    {
        $historique = HistoriquePoste::findOrFail($code_historique);

        $request->validate([
            'code_poste' => 'required|string|exists:poste,code_poste',
            'code_service' => 'required|string|exists:service,code_service',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after_or_equal:date_debut'
        ]);

        $historique->update($request->only(['code_poste', 'code_service', 'date_debut', 'date_fin']));

        return redirect()->route('historique_postes.show', $historique->matricule_employe)
                         ->with('success', 'Ligne de mutation modifiée avec succès.');
    }

    /**
     * DESTROY : Suppression d'une ligne de mutation précise
     */
    public function destroy(string $code_historique): RedirectResponse
    {
        $historique = HistoriquePoste::findOrFail($code_historique);
        $matricule = $historique->matricule_employe;
        
        $historique->delete();

        return redirect()->route('historique_postes.show', $matricule)
                         ->with('success', 'Ligne de mutation supprimée de l\'historique.');
    }
}