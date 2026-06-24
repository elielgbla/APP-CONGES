<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Demande;
use App\Models\Employe;

class WorkflowServiceProvider extends ServiceProvider
{
    /**
     * Enregistre les services dans le conteneur.
     */
    public function register()
    {
        $this->app->singleton('workflow', function ($app) {
            return new class {

                /**
                 * Fait progresser ou rejeter une demande de congé à travers les 4 échelons.
                 */
                public function passerEtape($code_demande, $matricule_employe, $action, $commentaire = null)
                {
                    // 1. Récupérer la demande et l'utilisateur connecté avec ses relations chargées
                    $demande = Demande::where('code_demande', $code_demande)->firstOrFail();
                    $user = Auth::user();

                    if (!$user) {
                        return false;
                    }

                    // HARMONISATION : Utilisation de l'accesseur dynamique depuis l'historique
                    $posteActuel = $user->code_poste; 

                    // 2. Gestion stricte du REJET
                    if ($action === 'rejeter') {
                        $demande->update([
                            'statut_demande' => 'rejetee',
                            'commentaire_validateur' => $commentaire
                        ]);
                        return true;
                    }

                    // 3. Gestion de l'APPROBATION selon les codes postes harmonisés
                    switch ($demande->statut_demande) {
                        
                        case 'en_attente_n1':
                            if ($posteActuel === 'RESP_SERVI' || $posteActuel === 'RESPONSABLE') {
                                $demande->update([
                                    'statut_demande' => 'en_attente_n2',
                                    'commentaire_validateur' => $commentaire
                                ]);
                                return true;
                            }
                            break;

                        case 'en_attente_n2':
                            if ($posteActuel === 'RH') {
                                $demande->update([
                                    'statut_demande' => 'en_attente_n3',
                                    'commentaire_validateur' => $commentaire
                                ]);
                                return true;
                            }
                            break;

                        case 'en_attente_n3':
                            if ($posteActuel === 'DIR_EXPLOI' || $posteActuel === 'DIR_EXPLOITATION') {
                                $demande->update([
                                    'statut_demande' => 'en_attente_n4',
                                    'commentaire_validateur' => $commentaire
                                ]);
                                return true;
                            }
                            break;

                        case 'en_attente_n4':
                            if ($posteActuel === 'DIR_GENERA' || $posteActuel === 'DIR_GENERAL') {
                                
                                DB::transaction(function () use ($demande, $commentaire) {
                                    $demande->update([
                                        'statut_demande' => 'approuvee',
                                        'commentaire_validateur' => $commentaire
                                    ]);

                                    $debut = \Carbon\Carbon::parse($demande->date_debut_prevu);
                                    $fin = \Carbon\Carbon::parse($demande->date_fin_prevu);
                                    $joursConsommes = $debut->diffInDays($fin) + 1;

                                    $employe = Employe::where('matricule_employe', $demande->matricule_employe)->first();
                                    if ($employe) {
                                        $employe->decrement('total_conge_restant', $joursConsommes);
                                    }
                                });

                                return true;
                            }
                            break;
                    }

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