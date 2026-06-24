<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categorie extends Model
{
    protected $table = 'categorie';
    protected $primaryKey = 'code_categorie';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['code_categorie', 'libelle_categorie'];

    public function employes(): HasMany
    {
        return $this->hasMany(Employe::class, 'code_categorie', 'code_categorie');
    }
}