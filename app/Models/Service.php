<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Service extends Model
{
    protected $table = 'service';
    protected $primaryKey = 'code_service';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['code_service', 'libelle_service'];

    public function employes(): BelongsToMany
    {
        return $this->belongsToMany(
            Employe::class,
            'historique_poste',  // Le nom exact de ta table dans phpMyAdmin
            'code_service',      // La clé étrangère de la table service dans le pivot
            'matricule_employe'  // La clé étrangère de la table employe dans le pivot
        );
    }
}