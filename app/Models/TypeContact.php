<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeContact extends Model
{
    use HasFactory;

    protected $table = 'type_contact';
    protected $primaryKey = 'code_type_contact';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'code_type_contact',
        'libelle_type_contact',
        'description',
    ];

    /**
     * Relation 1:N vers les contacts des employés.
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'code_type_contact', 'code_type_contact');
    }
}