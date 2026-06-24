<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDemandeRequest;
use App\Models\Demande;
use App\Models\Employe;
use App\Models\TypePermission;
use App\Services\WorkflowPermissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class DemandeController extends Controller
{
    protected WorkflowPermissionService $workflowService;

    public function __construct(WorkflowPermissionService $workflowService)
    {
        $this->workflowService = $workflowService;
    }

    /**
     * INDEX : Liste filtrée avec circuit de validation dynamique
     */
    public function index(Request $request)
    {
        $recherche = $request->input('search');
        
        // Charger l'employé connecté de façon robuste
        $user = Employe::where('matricule_employe', Auth::user()->matricule_employe)->firstOrFail();

        $demandes = $this->workflowService->getDemandesVisiblesPourUser($user, $recherche)
            ->orderBy('date_demande', 'desc')
            ->paginate(10);

        // Charger le service actif de l'utilisateur connecté via l'historique
        $user->load(['historiquePostes' => function ($q) {
            $q->whereNull('date_fin');
        }]);
        $historiqueUser = $user->historiquePostes->first();
        $codeService = $historiqueUser ? $historiqueUser->code_service : null;

        $employes = collect();

        switch ($user->code_poste) {
            case 'EMPLOYE':
                $employes = Employe::where('matricule_employe', $user->matricule_employe)->get();
                break;

            case 'RESP_SERVI':
            case 'RESPONSABLE':
                $employes = Employe::whereHas('historiquePostes', function ($query) use ($codeService) {
                    $query->where('historique_poste.code_service', $codeService)->whereNull('date_fin');
                })->get();
                break;

            case 'RH':
            case 'ADMIN':
                $employes = Employe::all();
                break;

            case 'DIR_EXPLOI':
            case 'DIR_GENERA':
                $employes = Employe::whereHas('historiquePostes.service', function ($query) {
                    $query->where('libelle_service', 'LIKE', '%Direction%')->whereNull('historique_poste.date_fin');
                })->get();
                break;
        }

        $types_permission = TypePermission::all(); 

        return view('demandes.index', compact('demandes', 'recherche', 'employes', 'types_permission'));
    }

    /**
     * CREATE : Filtrage pour la création
     */
    public function create()
    {
        $user = Employe::where('matricule_employe', Auth::user()->matricule_employe)->firstOrFail();
        $types_conge = TypePermission::all();
        
        $user->load(['historiquePostes' => function ($q) {
            $q->whereNull('date_fin');
        }]);
        $historiqueUser = $user->historiquePostes->first();
        $codeService = $historiqueUser ? $historiqueUser->code_service : null;

        $employes = collect();
        
        switch ($user->code_poste) {
            case 'EMPLOYE':
                $employes = Employe::where('matricule_employe', $user->matricule_employe)->get();
                break;

            case 'RESP_SERVI':
            case 'RESPONSABLE':
                $employes = Employe::whereHas('historiquePostes', function ($query) use ($codeService) {
                    $query->where('historique_poste.code_service', $codeService)->whereNull('date_fin');
                })->get();
                break;

            case 'RH':
            case 'ADMIN':
                $employes = Employe::all();
                break;

            case 'DIR_EXPLOI':
            case 'DIR_GENERA':
                $employes = Employe::whereHas('historiquePostes.service', function ($query) {
                    $query->where('libelle_service', 'LIKE', '%Direction%')->whereNull('historique_poste.date_fin');
                })->get();
                break;
        }

        return view('demandes.create', compact('types_conge', 'employes'));
    }

    /**
     * STORE : Création sécurisée
     */
    public function store(StoreDemandeRequest $request): RedirectResponse
    {
        $user = Employe::where('matricule_employe', Auth::user()->matricule_employe)->firstOrFail();
        $matriculeSoumis = $request->matricule_employe;

        if ($user->code_poste === 'EMPLOYE' && $matriculeSoumis !== $user->matricule_employe) {
            return redirect()->back()->withErrors(['matricule_employe' => 'Habilitation insuffisante.']);
        }

        if ($user->code_poste === 'RESP_SERVI' || $user->code_poste === 'RESPONSABLE') {
            $user->load(['historiquePostes' => function ($q) {
                $q->whereNull('date_fin');
            }]);
            $historiqueUser = $user->historiquePostes->first();
            $codeServiceUser = $historiqueUser ? $historiqueUser->code_service : null;

            $isSameService = Employe::where('matricule_employe', $matriculeSoumis)
                ->whereHas('historiquePostes', function ($query) use ($codeServiceUser) {
                    $query->where('historique_poste.code_service', $codeServiceUser)->whereNull('date_fin');
                })->exists();

            if (!$isSameService) {
                return redirect()->back()->withErrors(['matricule_employe' => 'Vous pouvez uniquement soumettre pour les agents de votre service.']);
            }
        }

        Demande::create(array_merge($request->validated(), [
            'matricule_employe' => $matriculeSoumis,
            'statut_demande'    => 'en_attente_n1'
        ]));

        return redirect()->route('demandes.index')->with('success', 'Demande enregistrée.');
    }

    public function show(string $code_demande)
    {
        $demande = Demande::with([
            'employe.historiquePostes.poste', 
            'employe.historiquePostes.service', 
            'typePermission', 
            'validations.valideur'
        ])->findOrFail($code_demande);
        
        $historiqueValidations = $demande->validations->keyBy('niveau_validation');

        return view('demandes.show', compact('demande', 'historiqueValidations'));
    }

    public function edit(string $code_demande)
    {
        $demande = Demande::findOrFail($code_demande);

        if (!in_array($demande->statut_demande, ['en_attente_n1', 'rejetee'])) {
            return redirect()->route('demandes.index')->with('error', 'Modification impossible.');
        }

        $types_conge = TypePermission::all();
        $employes = Employe::where('matricule_employe', $demande->matricule_employe)->get();

        return view('demandes.edit', compact('demande', 'types_conge', 'employes'));
    }

    public function update(StoreDemandeRequest $request, string $code_demande): RedirectResponse
    {
        $demande = Demande::findOrFail($code_demande);

        if (!in_array($demande->statut_demande, ['en_attente_n1', 'rejetee'])) {
            return redirect()->route('demandes.index')->with('error', 'Dossier verrouillé.');
        }

        $demande->update($request->validated());

        return redirect()->route('demandes.show', $code_demande)->with('success', 'Demande rectifiée.');
    }

    public function destroy(string $code_demande): RedirectResponse
    {
        $demande = Demande::findOrFail($code_demande);
        
        if ($demande->statut_demande !== 'en_attente_n1') {
            return redirect()->route('demandes.index')->with('error', 'Suppression impossible.');
        }

        $demande->delete();
        return redirect()->route('demandes.index')->with('success', 'Dossier supprimé.');
    }

   public function valider(Request $request, string $code_demande): RedirectResponse
{
    $user = Employe::where('matricule_employe', Auth::user()->matricule_employe)->firstOrFail();

    // SÉCURITÉ : Bloquer l'admin directement au niveau du contrôleur
    if ($user->code_poste === 'ADMIN') {
        return redirect()->back()->with('error', 'Action impossible : En tant qu\'administrateur, vous êtes uniquement observateur.');
    }

    $request->validate([
        'action' => 'required|in:approuver,rejeter',
        'commentaire' => 'required|string|max:255'
    ]);

    $demande = Demande::findOrFail($code_demande);
    
    $data = [
        'decision' => $request->action,
        'commentaire' => $request->commentaire
    ];

    $this->workflowService->validerEtape($demande, $user, $data);

    return redirect()->route('demandes.show', $code_demande)->with('success', 'Décision enregistrée avec succès.');
}

    public function attestation(string $code_demande)
    {
        $demande = Demande::with(['employe.historiquePostes.poste', 'employe.historiquePostes.service', 'typePermission', 'validations.valideur'])->findOrFail($code_demande);

        if ($demande->statut_demande !== 'approuvee') {
            return redirect()->route('demandes.show', $code_demande)->with('error', "L'attestation n'est pas disponible.");
        }

        $validations = $demande->validations->keyBy('niveau_validation');

        return view('demandes.attestation', compact('demande', 'validations'));
    }

    public function imprimerRegistre(Request $request)
    {
        $statut = $request->input('statut');
        $user = Employe::where('matricule_employe', Auth::user()->matricule_employe)->firstOrFail();

        $query = $this->workflowService->getDemandesVisiblesPourUser($user);

        if (!empty($statut) && $statut !== 'tous') {
            $query->where('statut_demande', $statut);
        }

        $demandes = $query->orderBy('date_demande', 'desc')->get();

        return view('demandes.imprimer_registre', compact('demandes', $statut));
    }

    public function imprimerUnique(string $code_demande)
    {
        $demande = Demande::with(['employe.historiquePostes.poste', 'employe.historiquePostes.service', 'typePermission', 'validations.valideur'])->findOrFail($code_demande);

        return view('demandes.imprimer_unique', compact('demande'));
    }
}