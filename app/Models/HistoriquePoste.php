<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class HistoriquePoste extends Model
{
    protected $table = 'historique_poste';

    public $timestamps = false;
   // Définir le nom exact de la clé primaire
    protected $primaryKey = 'code_historique';

    // Préciser à Eloquent qu'elle n'est pas auto-incrémentée par MySQL (car c'est un VARCHAR/String)
    public $incrementing = false;
    protected $keyType = 'string';

    // Autoriser le champ à être assigné en masse
    protected $fillable = [
        'code_historique', // <-- TRÈS IMPORTANT
        'matricule_employe',
        'code_poste',
        'code_service',
        'date_debut',
        'date_fin'
    ];
    
    /**
     * Génération automatique de la clé primaire sécurisée
     */
    protected static function booted()
    {
        static::creating(function ($historique) {
            if (empty($historique->code_historique)) {
                // Récupère le dernier code_historique pour extraire le numéro le plus élevé
                $lastCode = DB::table('historique_poste')
                    ->select('code_historique')
                    ->orderBy('code_historique', 'desc')
                    ->first();

                if ($lastCode) {
                    // On extrait les chiffres du code (ex: HIS00015 -> 15) et on ajoute 1
                    $number = (int) substr($lastCode->code_historique, 3) + 1;
                } else {
                    $number = 1;
                }

                // Format strict : HIS + Position sur 5 chiffres (ex: HIS00001)
                $historique->code_historique = 'HIS' . str_pad($number, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function employe(): BelongsTo 
    { 
        return $this->belongsTo(Employe::class, 'matricule_employe', 'matricule_employe'); 
    }

    public function poste(): BelongsTo 
    { 
        return $this->belongsTo(Poste::class, 'code_poste', 'code_poste'); 
    }

    public function service(): BelongsTo 
    { 
        return $this->belongsTo(Service::class, 'code_service', 'code_service'); 
    }
}