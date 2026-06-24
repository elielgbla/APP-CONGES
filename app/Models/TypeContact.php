<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeContact extends Model
{
    protected $table = 'type_contact';
    protected $primaryKey = 'code_type_contact';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['code_type_contact', 'libelle_type_contact'];

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'code_type_contact', 'code_type_contact');
    }
}