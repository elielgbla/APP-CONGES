<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Residence extends Pivot
{
    // Nom exact de la table dans la base de données
    protected $table = 'residence';

    // Nom de la clé primaire
    protected $primaryKey = 'code_residence';

    // On désactive l'auto-incrément car c'est une chaîne de caractères
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'code_residence',
        'matricule_employe',
        'adresse',
        'code_localite',
    ];

    /**
     * Le "booted" method pour intercepter la création et générer le code automatiquement.
     */
    protected static function booted()
    {
        static::creating(function ($residence) {
            // Extraction des 3 premières lettres en majuscules (ex: 'residence' -> 'RES')
            $prefixe = strtoupper(substr($residence->getTable(), 0, 3));

            // CORRECTION INTELEPHENSE : On passe '*' en argument pour valider la signature attendue par l'éditeur
            $totalElements = static::count('*');

            $nouvelIndex = $totalElements + 1;

            // Formatage de l'index sur 3 chiffres (ex: 001, 012)
            $compteurFormate = str_pad($nouvelIndex, 3, '0', STR_PAD_LEFT);

            $residence->code_residence = $prefixe . $compteurFormate;
        });
    }

    /**
     * Relation : Une résidence appartient à un employé.
     */
    public function employe()
    {
        return $this->belongsTo(Employe::class, 'matricule_employe', 'matricule_employe');
    }

    /**
     * Relation : Une résidence appartient à une localité.
     */
    public function localite()
    {
        return $this->belongsTo(Localite::class, 'code_localite', 'code_localite');
    }
}