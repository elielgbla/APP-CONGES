<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Localite extends Model
{
    protected $table = 'localite';
    protected $primaryKey = 'code_localite';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['code_localite', 'libelle_localite'];

    /**
     * Cycle de vie du modèle : Génération automatique du code.
     */
    protected static function booted()
    {
        static::creating(function ($localite) {
            // 1. Extraire les 3 premières lettres du libellé pour le préfixe (ex: ABIDJAN -> ABJ)
            $prefixe = strtoupper(substr($localite->libelle_localite, 0, 3));

            // 2. Compter le nombre de localités existantes commençant par ce préfixe
            // pour générer un incrément (ex: ABJ-001, ABJ-002)
            $count = self::where('code_localite', 'LIKE', $prefixe . '-%')->count() + 1;

            // 3. Formater le code : PRE-00X
            $localite->code_localite = $prefixe . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
        });
    }

    public function residences(): HasMany
    {
        return $this->hasMany(Residence::class, 'code_localite', 'code_localite');
    }

    public function employesNis(): HasMany
    {
        return $this->hasMany(Employe::class, 'code_localite_naissance', 'code_localite');
    }
}