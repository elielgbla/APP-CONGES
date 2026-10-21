<?php

namespace App\Http\Controllers;

use App\Models\Validation;
use App\Models\Demande;
use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ValidationController extends Controller implements HasMiddleware
{
    /**
     * Définition des middlewares du contrôleur (Interface HasMiddleware)
     * Sécurise l'accès aux 4 niveaux de validation en respectant strictement la hiérarchie.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function (Request $request, $next) {
                $user = Auth::user();
                
                // 1. Sécurité de base
                if (!$user) {
                    abort(401, 'Veuillez vous connecter pour exécuter cette action.');
                }

                // 2. Blocage strict de l'Administrateur (Rôle purement consultatif/audit)
                if (strtoupper($user->code_poste) === 'ADMIN') {
                    abort(403, 'Validation impossible : L\'administrateur système possède uniquement des droits de consultation.');
                }

                // 3. Récupération du service actif du Valideur
                $user->load(['historiquePostes' => function($q) {
                    $q->whereNull('date_fin');
                }]);
                $historiqueValideur = $user->historiquePostes->first();
                $userService = $historiqueValideur ? $historiqueValideur->code_service : null;

                // 4. Extraction des paramètres
                $niveauSoumis = (int) $request->input('niveau_validation');
                $code_demande = $request->route('code_demande') ?? $request->input('code_demande');

                $demande = Demande::with(['employe.historiquePostes' => function($q) {
                    $q->whereNull('date_fin');
                }])->find($code_demande);

                if (!$demande || !$demande->employe) {
                    abort(404, 'Demande ou employé introuvable pour vérification des droits.');
                }

                if ($demande->matricule_employe === $user->matricule_employe) {
                    abort(403, 'Auto-validation interdite : cette demande doit être traitée par un autre valideur.');
                }

                if ($demande->statut_demande !== 'en_attente_n' . $niveauSoumis) {
                    abort(403, 'Opération refusée : le niveau soumis ne correspond pas à l’étape actuelle de la demande.');
                }

                $historiqueEmploye = $demande->employe->historiquePostes->first();
                $serviceEmploye = $historiqueEmploye ? $historiqueEmploye->code_service : null;
                $posteActif = strtoupper($user->code_poste);

                // 5. Application stricte des droits de validation selon le NOUVEAU circuit
                switch ($niveauSoumis) {
                    case 1:
                        // N1 : Chef de Service (doit être du même service que le demandeur)
                        if (!in_array($posteActif, ['RESP_SERVI', 'RESPONSABLE']) || $userService !== $serviceEmploye) {
                            abort(403, 'Accès interdit : Seul le Responsable de service de l\'agent peut poser le visa N1.');
                        }
                        break;

                    case 2:
                        // N2 : Ressources Humaines
                        if ($posteActif !== 'RH') {
                            abort(403, 'Accès interdit : Seul le service RH peut poser le visa N2.');
                        }
                        break;

                    case 3:
                        // N3 : Directeur d'Exploitation
                        if (!in_array($posteActif, ['DIR_EXPLOI', 'DIR_EXPLOITATION'])) {
                            abort(403, 'Accès interdit : Seul le Directeur d\'Exploitation peut poser le visa N3.');
                        }
                        break;

                    case 4:
                        // N4 : Directeur Général (visa final)
                        if (!in_array($posteActif, ['DIR_GENERA', 'DIR_GENERAL'])) {
                            abort(403, 'Accès interdit : Seul le Directeur Général peut poser le visa final N4.');
                        }
                        break;
                    
                    default:
                        abort(400, 'Niveau de validation soumis non reconnu dans le circuit de l\'entreprise.');
                }

                return $next($request);
            }),
        ];
    }

    /**
     * Enregistrer une décision de validation pour une demande
     * 
     * @param Request $request
     * @param string|null $code_demande (Supporte paramètre de route ou injection direct)
     * @return RedirectResponse
     */
    public function store(Request $request, string $code_demande = null): RedirectResponse
    {
        // 1. Validation des inputs
        $request->validate([
            'niveau_validation' => 'required|integer|between:1,4',
            'decision'          => 'required|in:valide,rejete,approuver,rejeter', // Prise en compte des alias UI
            'commentaire'       => 'nullable|string|max:500',
            'signature'         => 'nullable|string|max:255',
        ]);

        $code_demande = $code_demande ?? $request->input('code_demande');
        $demande = Demande::findOrFail($code_demande);
        $userConnected = Auth::user();

        // Harmonisation sémantique des décisions
        $decisionFinale = in_array($request->decision, ['valide', 'approuver']) ? 'valide' : 'rejete';
        $actionWorkflow = ($decisionFinale === 'valide') ? 'passer' : 'rejeter';
        $niveau = (int) $request->niveau_validation;

        // Contrôle anti-doublon pour empêcher l'erreur SQL d'intégrité (Code 1062)
        $validationExistante = Validation::where('code_demande', $demande->code_demande)
                                         ->where('niveau_validation', $niveau)
                                         ->exists();
        if ($validationExistante) {
            return redirect()->back()->with('error', "Action refusée : Le visa de niveau {$niveau} a déjà été enregistré (double soumission détectée).");
        }

        // 2. Transaction pour garantir l'intégrité de la validation ET de l'audit
        DB::transaction(function () use ($demande, $userConnected, $niveau, $decisionFinale, $actionWorkflow, $request) {
              
            // A. Création du visa dans la table validation_permission (le code_validation est auto-généré par le modèle)
            Validation::create([
                'code_demande'       => $demande->code_demande,
                'matricule_valideur' => $userConnected->matricule_employe,
                'niveau_validation'  => $niveau,
                'decision'           => $decisionFinale,
                'commentaire'        => $request->commentaire,
                'signature'          => $request->signature ?? null,
            ]);

            // B. Délégation de l'avancement (ou du rejet) au WorkflowServiceProvider
            // Cela gère automatiquement le changement de statut, la déduction des jours et la création du planning
            app('workflow')->passerEtape(
                $demande->code_demande, 
                $userConnected->matricule_employe, 
                $actionWorkflow, 
                $request->commentaire
            );

            // C. Journalisation (Audit) de l'action
            $logId = 'LOG' . sprintf('%07d', rand(1, 9999999));
            DB::table('journalisation')->insert([
                'code_log'          => $logId,
                'matricule_employe' => $userConnected->matricule_employe,
                'action'            => 'Visa Validation N' . $niveau,
                'table_concernee'   => 'demande, validation_permission',
                'date_action'       => now(),
                'details'           => "Visa [{$decisionFinale}] émis sur la demande {$demande->code_demande} (Niveau {$niveau}).",
                'ip_address'        => $request->ip()
            ]);
        });

        // 3. Redirection avec message contextuel
        if ($decisionFinale === 'rejete') {
            return redirect()->route('demandes.index')->with('success', 'La demande a été rejetée et le dossier est clos.');
        }

        if ($niveau === 4) {
            return redirect()->route('demandes.index')->with('success', 'Visa final accordé. Le planning d\'absence a été automatiquement généré.');
        }

        return redirect()->route('demandes.index')->with('success', 'Visa enregistré. La demande progresse au niveau suivant.');
    }
}