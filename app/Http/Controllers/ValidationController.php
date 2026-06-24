<?php

namespace App\Http\Controllers;

use App\Models\Validation;
use App\Models\Demande;
use App\Models\Planning;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ValidationController extends Controller implements HasMiddleware
{
    /**
     * Définition des middlewares du contrôleur (Nouvelle approche Laravel)
     * Sécurise l'accès aux 4 niveaux de validation selon le poste et le service.
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function (Request $request, $next) {
                $user = Auth::user();
                
                // 1. Sécurité de base : Utilisateur connecté obligatoire
                if (!$user) {
                    abort(401, 'Veuillez vous connecter pour exécuter cette action.');
                }

                // Les ADMIN ont un accès total et immédiat sur tout le circuit
                if ($user->code_poste === 'ADMIN') {
                    return $next($request);
                }

                // Charger l'historique de poste actif du VALIDEUR pour obtenir son code_service
                $user->load(['historiquePostes' => function($q) {
                    $q->whereNull('date_fin');
                }]);
                
                $historiqueValideur = $user->historiquePostes->first();
                $userService = $historiqueValideur ? $historiqueValideur->code_service : null;

                // 2. Récupération des paramètres de la requête
                $niveauSoumis = (int) $request->input('niveau_validation');
                $code_demande = $request->route('code_demande');

                // Récupération de la demande et du service actif de l'EMPLOYÉ demandeur
                $demande = Demande::with(['employe.historiquePostes' => function($q) {
                    $q->whereNull('date_fin');
                }])->find($code_demande);

                if (!$demande || !$demande->employe) {
                    abort(404, 'Demande ou employé introuvable pour vérification des droits.');
                }

                $historiqueEmploye = $demande->employe->historiquePostes->first();
                $serviceEmploye = $historiqueEmploye ? $historiqueEmploye->code_service : null;

                // 3. Application stricte des droits de validation par échelon (4 Niveaux)
                switch ($niveauSoumis) {
                    case 1:
                        // Le Niveau 1 requiert d'être RESP_SERVI du MÊME service que l'employé demandeur
                        if ($user->code_poste !== 'RESP_SERVI' || $userService !== $serviceEmploye) {
                            abort(403, 'Accès interdit : Seul le Responsable de service de l\'agent peut poser le visa N1.');
                        }
                        break;

                    case 2:
                        // Le Niveau 2 est réservé exclusivement au profil RH
                        if ($user->code_poste !== 'RH') {
                            abort(403, 'Accès interdit : Seul le service des Ressources Humaines peut poser le visa N2.');
                        }
                        break;

                    case 3:
                        // Le Niveau 3 est réservé exclusivement au Directeur d'Exploitation
                        if ($user->code_poste !== 'DIR_EXPLOI') {
                            abort(403, 'Accès interdit : Seul le Directeur d\'Exploitation peut poser le visa N3.');
                        }
                        break;

                    case 4:
                        // Le Niveau 4 est réservé exclusivement au Directeur Général
                        if ($user->code_poste !== 'DIR_GENERA') {
                            abort(403, 'Accès interdit : Seul le Directeur Général peut poser le visa N4.');
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
     * * @param Request $request
     * @param string $code_demande
     * @return RedirectResponse
     */
    public function store(Request $request, string $code_demande): RedirectResponse
    {
        // 1. Validation des inputs
        $request->validate([
            'niveau_validation' => 'required|integer|between:1,4',
            'decision' => 'required|in:valide,rejete',
            'commentaire' => 'nullable|string|max:255',
            'signature' => 'nullable|string|max:255',
        ]);

        $demande = Demande::findOrFail($code_demande);
        $userConnected = Auth::user();

        // 2. Enregistrement de la décision dans la table validation_permission
        Validation::create([
            'code_demande' => $demande->code_demande,
            'matricule_valideur' => $userConnected->matricule_employe,
            'niveau_validation' => (int) $request->niveau_validation,
            'decision' => $request->decision,
            'commentaire' => $request->commentaire,
            'signature' => $request->signature ?? $userConnected->signature ?? null,
        ]);

        // 3. Traitement en cas de rejet : arrêt immédiat du circuit
        if ($request->decision === 'rejete') {
            $demande->update(['statut_demande' => 'rejetee']);
            return redirect()->back()->with('success', 'La demande a été rejetée et le processus est clos.');
        }

        // 4. Progression dynamique à travers les 4 niveaux si la décision est 'valide'
        $niveau = (int) $request->niveau_validation;

        if ($niveau === 1) {
            // Validé par le Chef de Service (N1) -> passe en attente RH
            $demande->update(['statut_demande' => 'en_attente_n2']);
            
        } elseif ($niveau === 2) {
            // Validé par la RH (N2) -> passe en attente Directeur d'Exploitation
            $demande->update(['statut_demande' => 'en_attente_n3']);
            
        } elseif ($niveau === 3) {
            // Validé par le Dir Exploitation (N3) -> passe en attente Directeur Général
            $demande->update(['statut_demande' => 'en_attente_n4']);
            
        } elseif ($niveau === 4) {
            // APPROBATION FINALE ET DÉFINITIVE par le DG (N4)
            $demande->update(['statut_demande' => 'approuvee']);

            // 5. IMPACT AUTOMATIQUE SUR LE PLANNING DE L'AGENT
            // Génération du créneau d'absence (Exemple horaires SOGETHO)
            Planning::create([
                'matricule_employe' => $demande->matricule_employe,
                'date_debut' => $demande->date_debut_prevu,
                'date_fin' => $demande->date_fin_prevu,
                'heure_debut' => '07:30:00', 
                'heure_fin' => '17:00:00',   
            ]);
        }

        return redirect()->back()->with('success', 'Décision enregistrée avec succès.');
    }
}