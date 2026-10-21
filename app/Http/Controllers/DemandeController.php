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
use Illuminate\Support\Facades\DB;

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
        /* STREAMING_CHUNK: Initialisation et chargement de l'utilisateur connecté */
        $recherche = $request->input('search');
        
        // Charger l'employé connecté de façon robuste
        $user = Employe::where('matricule_employe', Auth::user()->matricule_employe)->firstOrFail();

        // Récupération des demandes visibles filtrées selon la logique métier globale
        $demandes = $this->workflowService->getDemandesVisiblesPourUser($user, $recherche)
            ->orderBy('date_demande', 'desc')
            ->paginate(10);

        // Charger le service actif de l'utilisateur connecté via son historique de poste
        $user->load(['historiquePostes' => function ($q) {
            $q->whereNull('date_fin');
        }]);
        $historiqueUser = $user->historiquePostes->first();
        $codeService = $historiqueUser ? $historiqueUser->code_service : null;

        $employes = collect();

        /* STREAMING_CHUNK: Filtrage dynamique des employés sélectionnables */
        switch ($user->code_poste) {
            case 'EMPLOYE':
            case 'DIR_EXPLOI':
            case 'DIR_EXPLOITATION':
            case 'DIR_GENERA':
            case 'DIR_GENERAL':
                // Selon les règles métier strictes, ces profils ne créent que pour eux-mêmes
                $employes = Employe::where('matricule_employe', $user->matricule_employe)->get();
                break;

            case 'RESP_SERVI':
            case 'RESPONSABLE':
                // Le responsable de service ne peut créer des demandes que pour lui ou son service actif
                $employes = Employe::whereHas('historiquePostes', function ($query) use ($codeService) {
                    $query->where('historique_poste.code_service', $codeService)->whereNull('date_fin');
                })->get();
                break;

            case 'RH':
            case 'ADMIN':
                // Les RH et administrateurs ont une visibilité totale sur l'ensemble de l'effectif actif
                $employes = Employe::where('statut_compte', 'actif')->get();
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
        /* STREAMING_CHUNK: Récupération des informations pour le formulaire de création */
        $user = Employe::where('matricule_employe', Auth::user()->matricule_employe)->firstOrFail();
        $types_conge = TypePermission::all();
        
        $user->load(['historiquePostes' => function ($q) {
            $q->whereNull('date_fin');
        }]);
        $historiqueUser = $user->historiquePostes->first();
        $codeService = $historiqueUser ? $historiqueUser->code_service : null;

        $employes = collect();
        
        // Distribution stricte du ciblage des employés à la création de demande
        switch ($user->code_poste) {
            case 'EMPLOYE':
            case 'DIR_EXPLOI':
            case 'DIR_EXPLOITATION':
            case 'DIR_GENERA':
            case 'DIR_GENERAL':
                // Ces profils ne créent que pour eux-mêmes
                $employes = Employe::where('matricule_employe', $user->matricule_employe)->get();
                break;

            case 'RESP_SERVI':
            case 'RESPONSABLE':
                // Menu déroulant restreint aux membres du service actif uniquement
                $employes = Employe::whereHas('historiquePostes', function ($query) use ($codeService) {
                    $query->where('historique_poste.code_service', $codeService)->whereNull('date_fin');
                })->get();
                break;

            case 'RH':
            case 'ADMIN':
                // Choix complet de tous les employés actifs
                $employes = Employe::where('statut_compte', 'actif')->get();
                break;
        }

        return view('demandes.create', compact('types_conge', 'employes'));
    }

    /**
     * STORE : Création sécurisée et anti-injection de privilèges
     */
    public function store(StoreDemandeRequest $request): RedirectResponse
    {
        /* STREAMING_CHUNK: Validation et filtrage sécurisé à l'enregistrement */
        $user = Employe::where('matricule_employe', Auth::user()->matricule_employe)->firstOrFail();
        $matriculeSoumis = $request->matricule_employe;
        $demandeur = Employe::where('matricule_employe', $matriculeSoumis)->firstOrFail();

        // Restriction stricte de création pour simple employé et directions opérationnelles
        $profilsRestreints = ['EMPLOYE', 'DIR_EXPLOI', 'DIR_EXPLOITATION', 'DIR_GENERA', 'DIR_GENERAL'];
        if (in_array($user->code_poste, $profilsRestreints) && $matriculeSoumis !== $user->matricule_employe) {
            return redirect()->back()->withErrors(['matricule_employe' => 'Habilitation insuffisante : vous ne pouvez soumettre une demande que pour vous-même.']);
        }

        // Restriction stricte pour les responsables de service
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
                return redirect()->back()->withErrors(['matricule_employe' => 'Vous pouvez uniquement soumettre pour les agents de votre propre service.']);
            }
        }

        $demande = Demande::create(array_merge($request->validated(), [
            'matricule_employe' => $matriculeSoumis,
            'statut_demande'    => $this->workflowService->getStatutInitial($demandeur)
        ]));

        if ($matriculeSoumis === $user->matricule_employe) {
            app('workflow')->initialiserDemande($demande, $demandeur);
        }

        return redirect()->route('demandes.index')->with('success', 'Votre demande de permission a bien été soumise.');
    }

    /**
     * SHOW : Consultation détaillée d'un dossier
     */
    public function show(string $code_demande)
    {
        /* STREAMING_CHUNK: Affichage détaillé de la demande et de ses validations */
        $demande = Demande::with([
            'employe.historiquePostes.poste', 
            'employe.historiquePostes.service', 
            'typePermission', 
            'validations'
        ])->findOrFail($code_demande);
        
        $historiqueValidations = $demande->validations->keyBy('niveau_validation');

        return view('demandes.show', compact('demande', 'historiqueValidations'));
    }

    /**
     * EDIT : Édition restreinte aux dossiers non verrouillés
     */
    public function edit(string $code_demande)
    {
        /* STREAMING_CHUNK: Récupération des informations pour modification */
        $demande = Demande::findOrFail($code_demande);

        if ($demande->matricule_employe !== Auth::user()->matricule_employe) {
            abort(403, 'Vous ne pouvez modifier que vos propres demandes.');
        }

        if (!in_array($demande->statut_demande, ['en_attente_n1', 'en_attente_n2', 'rejetee'])
            || ($demande->statut_demande !== 'rejetee' && $demande->validations()->exists())) {
            return redirect()->route('demandes.index')->with('error', 'Modification impossible : Ce dossier a déjà franchi des étapes de validation.');
        }

        $types_conge = TypePermission::all();
        $employes = Employe::where('matricule_employe', $demande->matricule_employe)->get();

        return view('demandes.edit', compact('demande', 'types_conge', 'employes'));
    }

    /**
     * UPDATE : Mise à jour sécurisée des valeurs
     */
    public function update(StoreDemandeRequest $request, string $code_demande): RedirectResponse
    {
        /* STREAMING_CHUNK: Mise à jour sécurisée de la demande */
        $demande = Demande::findOrFail($code_demande);

        if ($demande->matricule_employe !== Auth::user()->matricule_employe) {
            abort(403, 'Vous ne pouvez modifier que vos propres demandes.');
        }

        if (!in_array($demande->statut_demande, ['en_attente_n1', 'en_attente_n2', 'rejetee'])
            || ($demande->statut_demande !== 'rejetee' && $demande->validations()->exists())) {
            return redirect()->route('demandes.index')->with('error', 'Modification impossible : Dossier verrouillé.');
        }

        $relanceApresRejet = $demande->statut_demande === 'rejetee';
        $demande->update($request->validated());

        if ($relanceApresRejet || $demande->wasChanged()) {
            $demandeur = Employe::where('matricule_employe', $demande->matricule_employe)->firstOrFail();
            $demande->validations()->delete();
            $demande->update([
                'statut_demande' => $this->workflowService->getStatutInitial($demandeur),
            ]);

            if ($demandeur->matricule_employe === Auth::user()->matricule_employe) {
                app('workflow')->initialiserDemande($demande, $demandeur);
            }
        }

        return redirect()->route('demandes.show', $code_demande)->with('success', 'La demande a été mise à jour avec succès.');
    }

    /**
     * DESTROY : Suppression des demandes non encore validées
     */
    public function destroy(string $code_demande): RedirectResponse
    {
        /* STREAMING_CHUNK: Suppression sécurisée */
        $demande = Demande::findOrFail($code_demande);

        if ($demande->matricule_employe !== Auth::user()->matricule_employe) {
            abort(403, 'Vous ne pouvez supprimer que vos propres demandes.');
        }
        
        if ($demande->statut_demande !== 'en_attente_n1') {
            return redirect()->route('demandes.index')->with('error', 'Suppression impossible : La demande est déjà en cours de validation.');
        }

        $demande->delete();
        return redirect()->route('demandes.index')->with('success', 'La demande a été supprimée.');
    }

    /**
     * VALIDER : Soumission hiérarchique au workflow
     */
    public function valider(Request $request, string $code_demande): RedirectResponse
    {
        /* STREAMING_CHUNK: Traitement du visa de validation */
        $user = Employe::where('matricule_employe', Auth::user()->matricule_employe)->firstOrFail();

        // Sécurité de haut niveau : l'administrateur système ne participe pas activement à la validation
        if ($user->code_poste === 'ADMIN') {
            return redirect()->back()->with('error', 'Action non autorisée : l\'administrateur ne valide pas les dossiers.');
        }

        $request->validate([
            'action'      => 'required|in:valide,rejete',
            'commentaire' => 'nullable|string|max:255'
        ]);

        $demande = Demande::findOrFail($code_demande);
        
        $data = [
            'decision'    => $request->action,
            'commentaire' => $request->commentaire
        ];

        // Délégation du visa de validation au service métier sécurisé
        $this->workflowService->validerEtape($demande, $user, $data);

        // Évolution de l'étape de validation via notre fournisseur de services
        app('workflow')->passerEtape($demande->code_demande, $user->matricule_employe, $request->action === 'valide' ? 'passer' : 'rejeter', $request->commentaire);

        DB::table('journalisation')->insert([
            'code_log'          => 'LOG' . sprintf('%07d', random_int(1, 9999999)),
            'matricule_employe' => $user->matricule_employe,
            'action'            => 'Visa Validation',
            'table_concernee'   => 'demande, validation_permission',
            'date_action'       => now(),
            'details'           => "Visa [{$request->action}] émis sur la demande {$demande->code_demande}.",
            'ip_address'        => $request->ip(),
        ]);

        return redirect()->route('demandes.show', $code_demande)->with('success', 'Votre visa de validation a bien été enregistré.');
    }

    /**
     * ATTESTATION : Génération de l'attestation finale pour impression
     */
    public function attestation(string $code_demande)
    {
        /* STREAMING_CHUNK: Chargement et édition de l'attestation */
        $demande = Demande::with([
            'employe.historiquePostes.poste', 
            'employe.historiquePostes.service', 
            'typePermission', 
            'validations'
        ])->findOrFail($code_demande);

        if ($demande->statut_demande !== 'approuvee') {
            return redirect()->route('demandes.show', $code_demande)->with('error', "L'attestation ne peut être émise que pour une demande intégralement approuvée.");
        }

        $validations = $demande->validations->keyBy('niveau_validation');

        return view('demandes.attestation', compact('demande', 'validations'));
    }

    /**
     * IMPRIMER REGISTRE : Impression groupée des demandes
     */
    public function imprimerRegistre(Request $request)
    {
        /* STREAMING_CHUNK: Impression globale filtrée */
        $statut = $request->input('statut');
        $user = Employe::where('matricule_employe', Auth::user()->matricule_employe)->firstOrFail();

        $query = $this->workflowService->getDemandesVisiblesPourUser($user);

        if (!empty($statut) && $statut !== 'tous') {
            $query->where('statut_demande', $statut);
        }

        $demandes = $query->orderBy('date_demande', 'desc')->get();

        return view('demandes.imprimer_registre', compact('demandes', 'statut'));
    }

    /**
     * IMPRIMER UNIQUE : Impression individuelle
     */
    public function imprimerUnique(string $code_demande)
    {
        /* STREAMING_CHUNK: Impression individuelle */
        $demande = Demande::with([
            'employe.historiquePostes.poste', 
            'employe.historiquePostes.service', 
            'typePermission', 
            'validations'
        ])->findOrFail($code_demande);

        return view('demandes.imprimer_unique', compact('demande'));
    }
}