<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    protected $table = 'contact';
    protected $primaryKey = 'code_contact';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['code_contact', 'numero_contact', 'matricule_employe', 'code_type_contact'];

    /**
     * Génération automatique de la clé primaire CNTXXXX
     */
    protected static function booted()
    {
        static::creating(function ($contact) {
            if (empty($contact->code_contact)) {
                $position = static::count() + 1;
                $contact->code_contact = 'CNT' . str_pad($position, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function employe(): BelongsTo 
    { 
        return $this->belongsTo(Employe::class, 'matricule_employe', 'matricule_employe'); 
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeContact::class, 'code_type_contact', 'code_type_contact');
    }
}