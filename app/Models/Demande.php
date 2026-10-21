<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Demande extends Model
{
    /**
     * Le nom de la table associée dans la base de données.
     */
    protected $table = 'demande';

    /**
     * La clé primaire associée à la table.
     */
    protected $primaryKey = 'code_demande';

    /**
     * Le type de la clé primaire.
     */
    protected $keyType = 'string';

    /**
     * Indique si la clé primaire est en incrémentation automatique.
     */
    public $incrementing = false;

    /**
     * Indique si le modèle doit gérer les colonnes standards created_at et updated_at.
     */
    public $timestamps = false;

    /**
     * Les attributs qui sont assignables en masse.
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
     * Les attributs qui doivent être convertis en types natifs (Casting).
     */
    protected $casts = [
        'date_demande'     => 'date',
        'date_debut_prevu' => 'date',
        'date_fin_prevu'   => 'date',
    ];

    /**
     * Instructions de cycle de vie lors de l'instanciation de l'entité.
     */
    protected static function booted()
    {
        static::creating(function ($demande) {
            // Génération automatique du code de demande textuel unique
            if (empty($demande->code_demande)) {
                $demande->code_demande = 'DM-' . strtoupper(substr(uniqid(), -8));
            }

            // Remplissage automatique de date_demande avec la date du jour si non fournie
            if (empty($demande->date_demande)) {
                $demande->date_demande = now()->toDateString();
            }
        });
    }

    // =========================================================================
    // RELATIONS ELOQUENT COHÉRENTES
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
     * Une demande peut recevoir plusieurs lignes de validation (circuit hiérarchique).
     */
    public function validations(): HasMany
    {
        return $this->hasMany(Validation::class, 'code_demande', 'code_demande');
    }

    // =========================================================================
    // ACCESSEURS PRATIQUES ET BUSINESS LOGIC (POUR FACILITER L'UI)
    // =========================================================================

    /**
     * Calcule automatiquement la durée totale en jours pour la demande.
     * Exemple d'utilisation dans les vues : $demande->nombre_jours
     */
    public function getNombreJoursAttribute(): int
    {
        if (!$this->date_debut_prevu || !$this->date_fin_prevu) {
            return 0;
        }

        $debut = Carbon::parse($this->date_debut_prevu);
        $fin = Carbon::parse($this->date_fin_prevu);

        return (int) $debut->diffInDays($fin) + 1;
    }

    /**
     * Helpers booléens pour tester l'état actuel de la demande dans vos templates Blade.
     */
    public function isApprouvee(): bool
    {
        return $this->statut_demande === 'approuvee';
    }

    public function isRejetee(): bool
    {
        return $this->statut_demande === 'rejetee';
    }

    public function isEnAttente(): bool
    {
        return in_array($this->statut_demande, [
            'en_attente_n1',
            'en_attente_n2',
            'en_attente_n3',
            'en_attente_n4'
        ]);
    }
}