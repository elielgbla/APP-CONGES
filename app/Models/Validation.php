<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Validation extends Model
{
    /**
     * Le nom de la table associée dans la base de données.
     * Conforme à ta structure SQL (validations).
     */
    protected $table = 'validation_permission';

    /**
     * La clé primaire associée à la table.
     */
    protected $primaryKey = 'code_validation';

    /**
     * Le type de la clé primaire.
     */
    protected $keyType = 'string';

    /**
     * Désactivation de l'incrémentation automatique (géré par l'événement booted).
     */
    public $incrementing = false;

    /**
     * Désactivé car tu stockes la date dans la colonne personnalisée 'date_decision'.
     */
    public $timestamps = false;

    /**
     * Attributs assignables en masse.
     * Intègre désormais la colonne 'signature' (chemin du fichier sur le PC).
     */
    protected $fillable = [
        'code_validation',
        'code_demande',
        'matricule_valideur',
        'niveau_validation',
        'decision',
        'date_decision',
        'commentaire',
        'signature'
    ];

    /**
     * Cast des types natifs.
     * Alignement sur le tinyint de la base de données pour niveau_validation.
     */
    protected $casts = [
        'date_decision' => 'date',
        'niveau_validation' => 'integer'
    ];

    /**
     * Génération automatique du code de validation et de la date au moment de la création.
     */
    protected static function booted()
    {
        static::creating(function ($validation) {
            // Génération du code unique de validation (Ex: VAL-65EFA41B)
            if (empty($validation->code_validation)) {
                $validation->code_validation = 'VAL-' . strtoupper(substr(uniqid(), -8));
            }

            // Remplissage de la date de décision si non fournie
            if (empty($validation->date_decision)) {
                $validation->date_decision = now()->toDateString();
            }

            // Sécurité Workflow : Si la valeur envoyée du contrôleur est 'approuver',
            // on la convertit en 'valide' pour correspondre à ton ENUM MySQL
            if ($validation->decision === 'approuver') {
                $validation->decision = 'valide';
            } elseif ($validation->decision === 'rejeter') {
                $validation->decision = 'rejete';
            }
        });

        static::updating(function ($validation) {
            // Même sécurité de conversion pour les mises à jour (updateOrCreate)
            if ($validation->decision === 'approuver') {
                $validation->decision = 'valide';
            } elseif ($validation->decision === 'rejeter') {
                $validation->decision = 'rejete';
            }
        });
    }

    // =========================================================================
    // RELATIONS ELOQUENT
    // =========================================================================

    /**
     * Une validation concerne une seule demande.
     */
    public function demande(): BelongsTo
    {
        return $this->belongsTo(Demande::class, 'code_demande', 'code_demande');
    }

    /**
     * Une validation est enregistrée par un employé spécifique (le valideur).
     */
    public function valideur(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'matricule_valideur', 'matricule_employe');
    }
}
