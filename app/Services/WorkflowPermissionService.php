<?php

namespace App\Services;

use App\Models\Demande;
use App\Models\Validation;
use App\Models\Employe;
use Illuminate\Database\Eloquent\Builder;

class WorkflowPermissionService
{

    /**
     * Retourne le niveau de validation mappé logiciellement pour chaque poste actif.
     * Cette approche garantit la sécurité du nouveau circuit indépendamment des anciennes valeurs en DB.
     */
    public function getNiveauValidationParPoste(string $codePoste): ?int
    {
        return match (strtoupper($codePoste)) {
            'RESP_SERVI', 'RESPONSABLE'      => 1, // Chef de Service
            'RH'                             => 2, // Ressources Humaines
            'DIR_EXPLOI', 'DIR_EXPLOITATION' => 3, // Directeur d'Exploitation
            'DIR_GENERA', 'DIR_GENERAL'      => 4, // Directeur Général
            default                          => null,
        };
    }

    public function getStatutInitial(Employe $demandeur): string
    {
        $codePoste = strtoupper($demandeur->code_poste);

        if ($codePoste === 'EMPLOYE' && $this->estAffecteAuServiceRh($demandeur)) {
            return 'en_attente_n2';
        }

        if ($codePoste === 'DG' || in_array($codePoste, ['DIR_GENERA', 'DIR_GENERAL'])) {
            return 'en_attente_n4';
        }

        if (in_array($codePoste, ['DIR_EXPLOI', 'DIR_EXPLOITATION'])) {
            return 'en_attente_n4';
        }

        if ($codePoste === 'RH' && $demandeur->estResponsable()) {
            return 'en_attente_n3';
        }

        if ($codePoste === 'RH' || in_array($codePoste, ['RESP_SERVI', 'RESPONSABLE'])) {
            return 'en_attente_n2';
        }

        return 'en_attente_n1';
    }

    public function estAffecteAuServiceRh(Employe $employe): bool
    {
        return strtoupper((string) $employe->code_service) === 'RH_SERV';
    }

    public function getNiveauxAutoValidation(Employe $demandeur): array
    {
        $codePoste = strtoupper($demandeur->code_poste);

        if ($codePoste === 'DG' || in_array($codePoste, ['DIR_GENERA', 'DIR_GENERAL'])) {
            return [1, 2, 3, 4];
        }

        if (in_array($codePoste, ['DIR_EXPLOI', 'DIR_EXPLOITATION'])) {
            return [1, 2, 3];
        }

        if (in_array($codePoste, ['RESP_SERVI', 'RESPONSABLE'])) {
            return [1];
        }

        if ($codePoste === 'RH' && $demandeur->estResponsable()) {
            return [1, 2];
        }

        return [];
    }


    /**
     * Obtenir les demandes filtrées selon le rôle hiérarchique de l'utilisateur connecté
     */
    public function getDemandesVisiblesPourUser(Employe $user, ?string $recherche = null): Builder
    {
        // Chargement optimisé des relations requises pour l'affichage de l'index
        $query = Demande::with(['employe.historiquePostes.poste', 'employe.historiquePostes.service', 'typePermission']);

        $codePoste = strtoupper($user->code_poste);

        // Extraction sécurisée du service actif du valideur connecté via son historique de postes
        $user->load(['historiquePostes' => function ($q) {
            $q->whereNull('date_fin');
        }]);
        $historiqueValideur = $user->historiquePostes->first();
        $codeServiceValideur = $historiqueValideur ? $historiqueValideur->code_service : null;

        // Filtrage strict basé sur les statuts du nouveau workflow séquentiel (N1 -> N2 -> N3 -> N4 -> Approuvée)
        switch ($codePoste) {
            case 'EMPLOYE':
                // Un employé ne voit strictement que ses propres demandes
                $query->where('matricule_employe', $user->matricule_employe);
                break;

            case 'RESP_SERVI':
            case 'RESPONSABLE':
                // Niveau 1 : Voit ses propres demandes ET les demandes en attente de visa N1 du même service
                $query->where(function (Builder $q) use ($user, $codeServiceValideur) {
                    $q->where('matricule_employe', $user->matricule_employe)
                      ->orWhere(function (Builder $subQ) use ($codeServiceValideur) {
                          $subQ->where('statut_demande', 'en_attente_n1')
                               ->whereHas('employe.historiquePostes', function ($hpQ) use ($codeServiceValideur) {
                                   $hpQ->where('historique_poste.code_service', $codeServiceValideur)
                                       ->whereNull('historique_poste.date_fin');
                               });
                      });
                });
                break;

            case 'DIR_EXPLOI':
            case 'DIR_EXPLOITATION':
                // Niveau 3 : Voit ses propres demandes ET toutes les demandes nationales en attente de validation N3
                $query->where(function (Builder $q) use ($user) {
                    $q->where('matricule_employe', $user->matricule_employe)
                      ->orWhere('statut_demande', 'en_attente_n3');
                });
                break;

            case 'DIR_GENERA':
            case 'DIR_GENERAL':
                // Niveau 4 : Voit ses propres demandes ET toutes les demandes nationales en attente de validation N4
                $query->where(function (Builder $q) use ($user) {
                    $q->where('matricule_employe', $user->matricule_employe)
                      ->orWhere('statut_demande', 'en_attente_n4');
                });
                break;

            case 'RH':
                // Les RH ont une visibilité globale sur tout le cycle des demandes.
                break;

            case 'ADMIN':
                // L'administrateur système possède une visibilité globale complète sans filtre.
                break;

            default:
                // Protection par repli : Aucun rôle valide détecté
                $query->whereRaw('1 = 0');
                break;
        }


        // Moteur de recherche globale sécurisé
        if (!empty($recherche)) {
            $query->where(function (Builder $q) use ($recherche) {
                $q->where('code_demande', 'LIKE', "%{$recherche}%")
                  ->orWhere('motif', 'LIKE', "%{$recherche}%")
                  ->orWhere('statut_demande', 'LIKE', "%{$recherche}%")
                  ->orWhereHas('employe', function (Builder $empQ) use ($recherche) {
                      $empQ->where('nom', 'LIKE', "%{$recherche}%")
                           ->orWhere('prenom', 'LIKE', "%{$recherche}%")
                           ->orWhere('matricule_employe', 'LIKE', "%{$recherche}%");
                  });
            });
        }

        return $query;
    }


    /**
     * Enregistrer une décision de validation pour le niveau courant de l'utilisateur connecté.
     */
    public function validerEtape(Demande $demande, Employe $valideur, array $data): Validation
    {
        $codePoste = strtoupper($valideur->code_poste);

        if ($demande->matricule_employe === $valideur->matricule_employe) {
            abort(403, 'Auto-validation interdite : cette demande doit être traitée par un autre valideur.');
        }

        // 1. Restriction absolue d'activité pour l'Administrateur système
        if ($codePoste === 'ADMIN') {
            abort(403, "Action interdite : L'administrateur système a un rôle purement d'observateur et ne peut pas valider de demande.");
        }

        // 2. Récupération sécurisée du niveau de validation via le mapping dynamique
        $niveauValidation = $this->getNiveauValidationParPoste($codePoste);

        if (is_null($niveauValidation)) {
            abort(403, "Votre poste ne possède aucun niveau de validation configuré dans le circuit actuel.");
        }

        if ($niveauValidation === 1 && strtoupper((string) $valideur->code_service) !== strtoupper((string) $demande->employe->code_service)) {
            abort(403, "Vous ne pouvez valider que les demandes de votre propre service.");
        }

        // 3. Contrôle strict de tour de rôle
        $statutAttendu = "en_attente_n" . $niveauValidation;
        if ($demande->statut_demande !== $statutAttendu) {
            abort(403, "Opération refusée : Ce dossier est actuellement au stade " . str_replace('_', ' ', $demande->statut_demande));
        }

        // Génération sécurisée de la clé primaire textuelle unique pour la table validation_permission
        $codeValidation = 'VAL-' . strtoupper(substr(uniqid(), -8));

        return Validation::create([
            'code_validation'    => $codeValidation,
            'code_demande'       => $demande->code_demande,
            'matricule_valideur' => $valideur->matricule_employe,
            'niveau_validation'  => $niveauValidation,
            'decision'           => $data['decision'] ?? ($data['action'] ?? 'valide'),
            'date_decision'      => now()->toDateString(),
            'commentaire'        => $data['commentaire'] ?? null,
            'signature'          => $data['signature'] ?? null,
        ]);
    }
}