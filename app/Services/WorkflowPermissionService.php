<?php

namespace App\Services;

use App\Models\Demande;
use App\Models\Validation;
use App\Models\Employe;
use Illuminate\Database\Eloquent\Builder;

class WorkflowPermissionService
{
    /**
     * Obtenir les demandes filtrées selon le rôle hiérarchique de l'utilisateur connecté
     */
    public function getDemandesVisiblesPourUser(Employe $user, ?string $recherche = null): Builder
    {
        $query = Demande::with(['employe.historiquePostes.poste', 'employe.historiquePostes.service', 'typePermission']);

        $codePoste = $user->code_poste;

        // 1. Extraction sécurisée du service actif du valideur connecté via son historique de postes
        $user->load(['historiquePostes' => function ($q) {
            $q->whereNull('date_fin');
        }]);
        $historiqueValideur = $user->historiquePostes->first();
        $codeServiceValideur = $historiqueValideur ? $historiqueValideur->code_service : null;

        // 2. Filtrage strict selon le circuit hiérarchique à 4 niveaux
        switch ($codePoste) {
            case 'EMPLOYE':
                $query->where('matricule_employe', $user->matricule_employe);
                break;

            case 'RESP_SERVI':
            case 'RESPONSABLE':
                // Niveau 1 : Demandes en attente N1 appartenant au même service actif
                $query->where('statut_demande', 'en_attente_n1')
                      ->whereHas('employe.historiquePostes', function ($q) use ($codeServiceValideur) {
                          $q->where('historique_poste.code_service', $codeServiceValideur)
                            ->whereNull('historique_poste.date_fin');
                      });
                break;

            case 'RH':
                $query->where('statut_demande', 'en_attente_n2');
                break;

            case 'DIR_EXPLOI':
            case 'DIR_EXPLOITATION':
                $query->where('statut_demande', 'en_attente_n3');
                break;

            case 'DIR_GENERA':
            case 'DIR_GENERAL':
                $query->where('statut_demande', 'en_attente_n4');
                break;

            case 'ADMIN':
                // L'admin voit tout sans restriction
                break;

            default:
                $query->whereRaw('1 = 0');
                break;
        }

        // 3. Moteur de recherche global (Nettoyé des variables conflictuelles pour le Linter)
        if (!empty($recherche)) {
            $query->where(function ($q) use ($recherche) {
                $q->where('code_demande', 'LIKE', "%{$recherche}%")
                  ->orWhere('motif', 'LIKE', "%{$recherche}%")
                  ->orWhere('statut_demande', 'LIKE', "%{$recherche}%")
                  ->orWhereHas('employe', function ($empQ) use ($recherche) {
                      $empQ->where('nom', 'LIKE', "%{$recherche}%")
                           ->orWhere('prenom', 'LIKE', "%{$recherche}%")
                           ->orWhere('matricule_employe', 'LIKE', "%{$recherche}%");
                  });
            });
        }

        return $query;
    }

    /**
     * Enregistrer une décision de validation (Le reste est délégué au circuit applicatif ou DB)
     */
    /**
     * Enregistrer une décision de validation (L'ADMIN est purement observateur)
     */
    public function validerEtape(Demande $demande, Employe $valideur, array $data): Validation
    {
        // 1. Blocage strict : L'ADMIN ne peut pas valider
    if ($valideur->code_poste === 'ADMIN') {
        abort(403, "Action interdite : L'administrateur système a un rôle purement d'observateur et ne peut pas valider de demande.");
    }

    // CORRECTION : On filtre 'date_fin' sur l'historique des postes, pas sur le poste lui-même
    $valideur->load(['historiquePostes' => function ($q) {
        $q->whereNull('date_fin'); // C'est ici que se trouve le champ date_fin !
    }, 'historiquePostes.poste']);

    // On récupère l'historique actif (celui qui n'a pas de date_fin)
    $historiqueValideur = $valideur->historiquePostes->first();
    $niveauValidation = ($historiqueValideur && $historiqueValideur->poste) ? $historiqueValideur->poste->niveau_validation : null;


    if (is_null($niveauValidation)) {
            abort(403, "Votre poste ne possède aucun niveau de validation configuré.");
        }

        // 2. Vérification stricte du tour de rôle
        $statutAttendu = "en_attente_n" . $niveauValidation;
        if ($demande->statut_demande !== $statutAttendu) {
            abort(403, "Opération refusée : Ce dossier est actuellement au stade : " . $demande->statut_demande);
        }

        return Validation::create([
            'code_demande'       => $demande->code_demande,
            'matricule_valideur' => $valideur->matricule_employe,
            'niveau_validation'  => $niveauValidation,
            'decision'           => $data['decision'] ?? $data['action'],
            'date_decision'      => now()->toDateString(),
            'commentaire'        => $data['commentaire'] ?? null,
            'signature'          => $data['signature'] ?? null,
        ]);
    }
}