<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Poste extends Model
{
    protected $table = 'poste';
    protected $primaryKey = 'code_poste';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'code_poste', 
        'libelle_poste', 
        'est_responsable', 
        'niveau_validation', 
        'acces_journalisation'
    ];

    /**
     * Récupérer toutes les lignes d'historique liées à ce poste
     */
    public function historiquePostes(): HasMany
    {
        return $this->hasMany(HistoriquePoste::class, 'code_poste', 'code_poste');
    }

    /**
     * Récupérer tous les employés liés à ce poste via la table historique_poste
     */
    public function employes(): HasManyThrough
    {
        return $this->hasManyThrough(
            Employe::class,
            HistoriquePoste::class,
            'code_poste',        // Clé étrangère sur la table historique_poste
            'matricule_employe', // Clé étrangère sur la table employe
            'code_poste',        // Clé locale sur la table poste
            'matricule_employe'  // Clé locale sur la table historique_poste
        );
    }
}