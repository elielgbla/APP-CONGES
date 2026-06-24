<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypePermission extends Model
{
    protected $table = 'type_permission';
    protected $primaryKey = 'code_type_permission';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['code_type_permission', 'libelle_type_permission'];

    public function demandes(): HasMany
    {
        return $this->hasMany(Demande::class, 'code_type_permission', 'code_type_permission');
    }
}