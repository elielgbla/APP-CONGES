<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employe extends Authenticatable
{
    use Notifiable;

    protected $table = 'employe';
    protected $primaryKey = 'matricule_employe';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'matricule_employe',
        'nom',
        'prenom',
        'date_naissance',
        'code_localite_naissance',
        'mot_de_passe',
        'statut_compte',
        'email',
        'photo',
        'total_conge_restant',
        'code_genre',
        'code_categorie',
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    public function getAuthPasswordName(): string
    {
        return 'mot_de_passe';
    }

    public function getAuthPassword(): string
    {
        return $this->mot_de_passe;
    }

    public function getRouteKeyName(): string
    {
        return 'matricule_employe';
    }

    /* -------------------------------------------------------------------------- */
    /* RELATIONS UN-À-PLUSIEURS                                                   */
    /* -------------------------------------------------------------------------- */

    public function genre(): BelongsTo
    {
        return $this->belongsTo(Genre::class, 'code_genre', 'code_genre');
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(Categorie::class, 'code_categorie', 'code_categorie');
    }

    public function localiteNaissance(): BelongsTo
    {
        return $this->belongsTo(Localite::class, 'code_localite_naissance', 'code_localite');
    }

    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class, 'matricule_employe', 'matricule_employe');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'matricule_employe', 'matricule_employe');
    }

    /**
     * Relation vers les plannings journaliers de l'employé
     */
    public function planningsJournaliers(): HasMany
    {
        return $this->hasMany(Planning::class, 'matricule_employe', 'matricule_employe');
    }

    public function historiquePostes(): HasMany
    {
        return $this->hasMany(HistoriquePoste::class, 'matricule_employe', 'matricule_employe');
    }

    public function residences(): HasMany
    {
        return $this->hasMany(Residence::class, 'matricule_employe', 'matricule_employe');
    }

    /**
     * Relation vers l'historique des actions (logs / journalisation)
     */
    public function journalisations(): HasMany
    {
        return $this->hasMany(Journalisation::class, 'matricule_employe', 'matricule_employe');
    }

    /* -------------------------------------------------------------------------- */
    /* RELATIONS PLUSIEURS-À-PLUSIEURS                                            */
    /* -------------------------------------------------------------------------- */

    public function localites()
    {
        return $this->belongsToMany(
            Localite::class,
            'residence',
            'matricule_employe',
            'code_localite'
        )->withPivot(['code_residence', 'adresse']);
    }
    /* -------------------------------------------------------------------------- */
    /* MÉTHODES DE CONTRÔLE D'ACCÈS ET ACCESSEURS                                 */
    /* -------------------------------------------------------------------------- */

    /**
     * Récupère l'affectation (historique) actuellement active.
     */
    public function affectationActuelle(): HasOne
    {
        return $this->hasOne(HistoriquePoste::class, 'matricule_employe', 'matricule_employe')
            ->whereNull('date_fin')
            ->latest('date_debut');
    }

    /**
     * Relation vers le Poste actuel (Harmonisé et compatible Liste + Unique)
     */
    public function poste()
    {
        return $this->hasOne(HistoriquePoste::class, 'matricule_employe', 'matricule_employe')
            ->whereNull('date_fin')
            ->latest('date_debut')
            ->join('poste', 'historique_poste.code_poste', '=', 'poste.code_poste')
            ->select('poste.*', 'historique_poste.matricule_employe');
    }

    /**
     * Relation vers le Service actuel (Harmonisé et compatible Liste + Unique)
     */
    public function service()
    {
        return $this->hasOne(HistoriquePoste::class, 'matricule_employe', 'matricule_employe')
            ->whereNull('date_fin')
            ->latest('date_debut')
            ->join('service', 'historique_poste.code_service', '=', 'service.code_service')
            ->select('service.*', 'historique_poste.matricule_employe');
    }

    /**
     * Accesseur Dynamique : $user->code_poste
     */
    public function getCodePosteAttribute(): string
    {
        $actuel = $this->affectationActuelle;
        return $actuel ? $actuel->code_poste : 'EMPLOYE';
    }

    /**
     * Accesseur Dynamique Sécurisé : $user->code_service
     */
    public function getCodeServiceAttribute(): ?string
    {
        $actuel = $this->affectationActuelle;
        return $actuel ? $actuel->code_service : null;
    }

    /**
     * Alias de compatibilité ascendante pour le Workflow et Services : $user->code_poste_actif
     */
    public function getCodePosteActifAttribute(): string
    {
        return $this->code_poste;
    }

    /**
     * Alias de compatibilité ascendante pour le Workflow et Services : $user->code_service_actif
     */
    public function getCodeServiceActifAttribute(): ?string
    {
        return $this->code_service;
    }

    /**
     * Détermine si l'employé possède un code_poste précis (Rôle)
     */
    public function hasRole(string $role): bool
    {
        return strtoupper($this->code_poste) === strtoupper($role);
    }

    /**
     * Vérifie si l'employé a un poste de niveau administrateur
     */
    public function estAdmin(): bool
    {
        $actuel = $this->affectationActuelle;
        return $actuel && $actuel->poste && $actuel->poste->acces_journalisation == 1;
    }

    /**
     * Détermine si l'utilisateur est Admin ou RH
     */
    public function estAdminOuRh(): bool
    {
        $posteActuel = $this->historiquePostes()->whereNull('date_fin')->first();
        
        if (!$posteActuel) {
            return false;
        }

        return in_array(strtoupper($posteActuel->code_poste), ['ADMIN', 'RH']);
    }

    /**
     * Vérifie si l'employé est responsable / manager
     */
    public function estResponsable(): bool
    {
        $actuel = $this->affectationActuelle;
        return $actuel && $actuel->poste && $actuel->poste->est_responsable == 1;
    }

    /**
     * Récupère le niveau de validation de l'employé (N1, N2, N3, N4 ou null)
     */
    public function niveauValidation(): ?int
    {
        $actuel = $this->affectationActuelle;
        return $actuel && $actuel->poste ? $actuel->poste->niveau_validation : null;
    }
}