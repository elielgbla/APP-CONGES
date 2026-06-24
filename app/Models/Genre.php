<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Genre extends Model
{
    protected $table = 'genre';
    protected $primaryKey = 'code_genre';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['code_genre', 'libelle_genre'];

    public function employes(): HasMany
    {
        return $this->hasMany(Employe::class, 'code_genre', 'code_genre');
    }
}