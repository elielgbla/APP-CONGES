<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Demande extends Model
{
    /**
     * Le nom de la table associée dans la base de données.
     */
    protected $table = 'demande';

    /**
     * La clé primaire associée à la table (Conforme au MLD).
     */
    protected $primaryKey = 'code_demande';

    /**
     * Le type de la clé primaire.
     */
    protected $keyType = 'string';

    /**
     * Indique si la clé primaire est en incrémentation automatique.
     * Désactivé car on génère un code textuel unique (ex: DM-8F3EA21B).
     */
    public $incrementing = false;

    /**
     * Indique si le modèle doit gérer les colonnes created_at et updated_at.
     * Désactivé car tu utilises la colonne personnalisée 'date_demande'.
     */
    public $timestamps = false;

    /**
     * Les attributs qui sont assignables en masse (Mass Assignment).
     */
    protected $fillable = [
        'code_demande',
        'date_demande',
        'date_debut_prevu',
        'date_fin_prevu',
        'motif',
        'statut_demande',
        'matricule_employe',
        'code_type_permission'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs (Cast).
     */
    protected $casts = [
        'date_demande' => 'date',
        'date_debut_prevu' => 'date',
        'date_fin_prevu' => 'date',
    ];

    /**
     * Les instructions de cycle de vie du modèle (Booted Event).
     */
    protected static function booted()
    {
        static::creating(function ($demande) {
            // Génération automatique du code de demande textuel
            if (empty($demande->code_demande)) {
                $demande->code_demande = 'DM-' . strtoupper(substr(uniqid(), -8));
            }

            // Remplissage automatique de 'date_demande' avec la date du jour
            if (empty($demande->date_demande)) {
                $demande->date_demande = now()->toDateString();
            }
        });
    }

    // =========================================================================
    // RELATIONS ELOQUENT
    // =========================================================================

    /**
     * Une demande appartient à un et un seul Employé.
     */
    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'matricule_employe', 'matricule_employe');
    }

    /**
     * Une demande est qualifiée par un seul Type de Permission.
     */
    public function typePermission(): BelongsTo
    {
        return $this->belongsTo(TypePermission::class, 'code_type_permission', 'code_type_permission');
    }
    
    /**
     * Une demande peut recevoir plusieurs lignes de validation (circuit multi-niveaux).
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class, 'code_demande', 'code_demande');
    }
}