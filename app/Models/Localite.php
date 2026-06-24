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

    public function residences(): HasMany
    {
        return $this->hasMany(Residence::class, 'code_localite', 'code_localite');
    }

    public function employesNis(): HasMany
    {
        return $this->hasMany(Employe::class, 'code_localite_naissance', 'code_localite');
    }
}