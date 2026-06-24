<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class Planning extends Model
{
    protected $table = 'planning_journalier_employe';
    protected $primaryKey = 'code_planning';
    protected $keyType = 'string';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'code_planning',
        'matricule_employe',
        'date_debut',
        'date_fin',
        'heure_debut',
        'heure_fin',
        'jour_repos_1',
        'jour_repos_2'
    ];

    /**
     * Génération automatique et sécurisée de la clé primaire standardisée
     */
    protected static function booted()
    {
        static::creating(function ($planning) {
            if (empty($planning->code_planning)) {
                // Recherche du code le plus élevé en base de données, trié par ordre décroissant
                $lastPlanning = static::query()
                    ->orderBy('code_planning', 'desc')
                    ->first();

                if ($lastPlanning) {
                    // Extraction de la partie numérique (ex: 'PLN0005' -> 5)
                    $lastNumber = (int) substr($lastPlanning->code_planning, 3);
                    $position = $lastNumber + 1;
                } else {
                    $position = 1; // Si la table est complètement vide
                }

                $planning->code_planning = 'PLN' . str_pad($position, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relation inverse : Un planning appartient à un unique employé
     */
    public function employe(): BelongsTo
    {
        return $this->belongsTo(Employe::class, 'matricule_employe', 'matricule_employe');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS DYNAMIQUES POUR LES COMPTEURS ET LE CALENDRIER
    |--------------------------------------------------------------------------
    */

    /**
     * Détermine la borne de fin pour les calculs (Aujourd'hui si le planning est en cours)
     */
    private function getBorneFinCalcul(): Carbon
    {
        $debut = Carbon::parse($this->date_debut);
        
        // Si la date_fin est NULL (ouvert), on prend aujourd'hui
        $fin = $this->date_fin ? Carbon::parse($this->date_fin) : Carbon::today();
        
        // Si aujourd'hui est entre le début et la fin, on s'arrête dynamiquement à aujourd'hui
        if (Carbon::today()->between($debut, $fin)) {
            return Carbon::today();
        }

        return $fin;
    }

    /**
     * Compteur : Jours de travail cumulés
     */
    public function getTotalJoursTravailAttribute(): int
    {
        $debut = Carbon::parse($this->date_debut);
        $dateBorneFin = $this->getBorneFinCalcul();

        // Sécurité si la date de début est dans le futur
        if ($debut->isAfter($dateBorneFin)) {
            return 0;
        }

        $period = CarbonPeriod::create($debut, $dateBorneFin);
        $repos = array_filter([$this->jour_repos_1, $this->jour_repos_2]);

        $joursTravail = 0;
        foreach ($period as $date) {
            if (!in_array($date->format('N'), $repos)) {
                $joursTravail++;
            }
        }

        return $joursTravail;
    }

    /**
     * Compteur : Jours de repos cumulés
     */
    public function getTotalJoursReposAttribute(): int
    {
        $debut = Carbon::parse($this->date_debut);
        $dateBorneFin = $this->getBorneFinCalcul();

        if ($debut->isAfter($dateBorneFin)) {
            return 0;
        }

        $period = CarbonPeriod::create($debut, $dateBorneFin);
        $repos = array_filter([$this->jour_repos_1, $this->jour_repos_2]);

        $joursRepos = 0;
        foreach ($period as $date) {
            if (in_array($date->format('N'), $repos)) {
                $joursRepos++;
            }
        }

        return $joursRepos;
    }

    /**
     * Compteur : Heures cumulées travaillées
     */
    public function getTotalHeuresTravailAttribute(): float
    {
        $heureDebut = Carbon::parse($this->heure_debut);
        $heureFin = Carbon::parse($this->heure_fin);

        // Calcul de la durée de la plage horaire journalière en heures
        $heuresParJour = $heureDebut->diffInMinutes($heureFin) / 60;

        // Gestion du cas où les horaires traversent minuit (ex: 22:00 à 06:00)
        if ($heureDebut->isAfter($heureFin)) {
            $heuresParJour = (1440 - $heureDebut->diffInMinutes($heureFin)) / 60;
        }

        return $this->total_jours_travail * $heuresParJour;
    }
}