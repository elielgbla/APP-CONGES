<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Residence extends Model
{
    use HasFactory;

    // Nom exact de la table dans la base de données
    protected $table = 'residence';

    // Clé primaire de la table
    protected $primaryKey = 'code_residence';

    // Clé auto-incrémentée numérique
    public $incrementing = true;
    protected $keyType = 'int';

    // Désactivation des colonnes d'horodatage created_at / updated_at
    public $timestamps = false;

    // Attributs modifiables (sans code_residence qui est géré automatiquement)
    protected $fillable = [
        'matricule_employe',
        'code_localite',
        'adresse',
    ];

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