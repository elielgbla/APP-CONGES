<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Models\Demande;
use App\Models\Employe;
use App\Models\Validation;

class WorkflowServiceProvider extends ServiceProvider
{
    /**
     * Enregistre les services dans le conteneur.
     */
    public function register()
    {
        $this->app->singleton('workflow', function ($app) {
            return new class {

                public function initialiserDemande(Demande $demande, Employe $demandeur): void
                {
                    $niveauxAuto = app(\App\Services\WorkflowPermissionService::class)
                        ->getNiveauxAutoValidation($demandeur);

                    DB::transaction(function () use ($demande, $demandeur, $niveauxAuto) {
                        foreach ($niveauxAuto as $niveau) {
                            Validation::create([
                                'code_demande' => $demande->code_demande,
                                'matricule_valideur' => $demandeur->matricule_employe,
                                'niveau_validation' => $niveau,
                                'decision' => 'valide',
                                'commentaire' => 'Validation automatique selon le rôle du demandeur.',
                            ]);
                        }

                        if (in_array(4, $niveauxAuto, true)) {
                            $demande->update(['statut_demande' => 'en_attente_n4']);

                            // Seul le DG auto-valide le niveau final. Le Directeur
                            // d'exploitation laisse la demande en attente N4.
                            if (in_array(strtoupper($demandeur->code_poste), ['DG', 'DIR_GENERA', 'DIR_GENERAL'], true)) {
                                $this->passerEtape($demande->code_demande, $demandeur->matricule_employe, 'passer');
                            }
                        }
                    });
                }

                /**
                 * Fait progresser ou rejeter une demande de congé à travers les 4 échelons de validation.
                 * Séquence : N1 (Chef Service) -> N2 (RH) -> N3 (Dir Exploitation) -> N4 (DG)
                 */
                public function passerEtape($code_demande, $matricule_employe, $action, $commentaire = null)
                {
                    // 1. Récupérer la demande et l'utilisateur connecté avec ses relations chargées
                    $demande = Demande::where('code_demande', $code_demande)->firstOrFail();
                    $user = Employe::where('matricule_employe', $matricule_employe)->first();

                    if (!$user) {
                        return false;
                    }

                    // Récupération sécurisée du code de poste actif
                    $posteActuel = strtoupper($user->code_poste); 

                    // 2. Gestion stricte du REJET
                    if ($action === 'rejeter') {
                        $demande->update([
                            'statut_demande' => 'rejetee',
                            'commentaire_validateur' => $commentaire // Conserver l'affectation si présente en base
                        ]);
                        return true;
                    }

                    // 3. Gestion de l'APPROBATION selon les codes postes harmonisés et le nouveau circuit
                    switch ($demande->statut_demande) {
                        
                        case 'en_attente_n1':
                            // Étape 1 : Validé uniquement par le Chef de Service
                            if ($posteActuel === 'RESP_SERVI' || $posteActuel === 'RESPONSABLE') {
                                $demande->update([
                                    'statut_demande' => 'en_attente_n2',
                                    'commentaire_validateur' => $commentaire
                                ]);
                                return true;
                            }
                            break;

                        case 'en_attente_n2':
                            // Étape 2 : Validé par les Ressources Humaines
                            if ($posteActuel === 'RH') {
                                $demande->update([
                                    'statut_demande' => 'en_attente_n3',
                                    'commentaire_validateur' => $commentaire
                                ]);
                                return true;
                            }
                            break;

                        case 'en_attente_n3':
                            // Étape 3 : Validé par le Directeur d'Exploitation
                            if ($posteActuel === 'DIR_EXPLOI' || $posteActuel === 'DIR_EXPLOITATION') {
                                $demande->update([
                                    'statut_demande' => 'en_attente_n4',
                                    'commentaire_validateur' => $commentaire
                                ]);
                                return true;
                            }
                            break;

                        case 'en_attente_n4':
                            // Étape 4 (Finale) : Validé par le Directeur Général
                            if ($posteActuel === 'DIR_GENERA' || $posteActuel === 'DIR_GENERAL') {
                                
                                DB::transaction(function () use ($demande, $commentaire) {
                                    // Mise à jour de la demande en statut final "approuvee"
                                    $demande->update([
                                        'statut_demande' => 'approuvee',
                                        'commentaire_validateur' => $commentaire
                                    ]);

                                    // Calcul des jours à déduire
                                    $debut = \Carbon\Carbon::parse($demande->date_debut_prevu);
                                    $fin = \Carbon\Carbon::parse($demande->date_fin_prevu);
                                    $joursConsommes = $debut->diffInDays($fin) + 1;

                                    // Seul le congé annuel consomme le solde de congé.
                                    if ($demande->code_type_permission === 'CONGE_AN') {
                                        $employe = Employe::where('matricule_employe', $demande->matricule_employe)->first();
                                        if ($employe) {
                                            $employe->decrement('total_conge_restant', $joursConsommes);
                                        }
                                    }

                                    // Création automatique du planning d'absence
                                    DB::table('planning_journalier_employe')->insert([
                                        'code_planning' => 'PLN-' . strtoupper(substr(uniqid(), -8)),
                                        'matricule_employe' => $demande->matricule_employe,
                                        'date_debut' => $demande->date_debut_prevu,
                                        'date_fin' => $demande->date_fin_prevu,
                                        'heure_debut' => '08:00:00',
                                        'heure_fin' => '17:00:00',
                                        'jour_repos_1' => 6, // Samedi par défaut
                                        'jour_repos_2' => 7, // Dimanche par défaut
                                    ]);
                                });

                                return true;
                            }
                            break;
                    }

                    // Si l'utilisateur n'a pas le bon rôle, on refuse l'action
                    abort(403, "Vous n'avez pas l'autorité hiérarchique requise pour traiter ce dossier à ce stade.");
                }
            };
        });
    }

    public function boot()
    {
        //
    }
}