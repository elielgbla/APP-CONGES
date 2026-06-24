<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Employe;
use App\Models\Demande;
use App\Models\HistoriquePoste;
use Illuminate\View\View;
use Carbon\Carbon;

class StatistiqueController extends Controller
{
    public function index(): View
    {
        // --- 1. STATISTIQUES UNITAIRES ---

        // Éffectifs et statuts des comptes
        $statsEmployes = Employe::select('statut_compte', DB::raw('count(*) as total'))
            ->groupBy('statut_compte')
            ->get()
            ->pluck('total', 'statut_compte');

        $effectifTotal = Employe::count();

        // Répartition par Genre (via jointure ou code direct)
        $repartitionGenre = Employe::select('code_genre', DB::raw('count(*) as total'))
            ->groupBy('code_genre')
            ->get();

        // Taux de validation des demandes
        $statsDemandes = Demande::select('statut_demande', DB::raw('count(*) as total'))
            ->groupBy('statut_demande')
            ->get()
            ->pluck('total', 'statut_demande');


        // --- 2. STATISTIQUES CROISÉES COMPLEXES ---

        // Répartition des employés actifs par Service (Direction)
        $employesParService = DB::table('historique_poste')
            ->join('service', 'historique_poste.code_service', '=', 'service.code_service')
            ->whereNull('historique_poste.date_fin')
            ->select('service.libelle_service', DB::raw('count(*) as total'))
            ->groupBy('service.libelle_service')
            ->orderBy('total', 'desc')
            ->get();

        // Moyenne d'âge par Service (Calcul dynamique basé sur la date de naissance)
        $anneeActuelle = Carbon::now()->year;
        $ageMoyenParService = DB::table('historique_poste')
            ->join('employe', 'historique_poste.matricule_employe', '=', 'employe.matricule_employe')
            ->join('service', 'historique_poste.code_service', '=', 'service.code_service')
            ->whereNull('historique_poste.date_fin')
            ->whereNotNull('employe.date_naissance')
            ->select(
                'service.libelle_service', 
                DB::raw("ROUND(AVG($anneeActuelle - YEAR(employe.date_naissance)), 1) as age_moyen")
            )
            ->groupBy('service.libelle_service')
            ->get();

        // Délai moyen de traitement des validations (en jours)
        $delaiTraitement = DB::table('validation_permission')
            ->join('demande', 'validation_permission.code_demande', '=', 'demande.code_demande')
            ->select(DB::raw('ROUND(AVG(DATEDIFF(validation_permission.date_decision, demande.date_demande)), 1) as moyenne_jours'))
            ->value('moyenne_jours');

        // Taux d'absentéisme théorique par catégorie (somme des jours de permissions approuvées)
        $joursAbsencesParCategorie = DB::table('demande')
            ->join('employe', 'demande.matricule_employe', '=', 'employe.matricule_employe')
            ->join('categorie', 'employe.code_categorie', '=', 'categorie.code_categorie')
            ->where('demande.statut_demande', '=', 'approuvee')
            ->select(
                'categorie.libelle_categorie',
                DB::raw('SUM(DATEDIFF(demande.date_fin_prevu, demande.date_debut_prevu) + 1) as total_jours')
            )
            ->groupBy('categorie.libelle_categorie')
            ->orderBy('total_jours', 'desc')
            ->get();


        // --- 3. TRANSMISSION AUX VUES ---
        return view('statistiques.index', compact(
            'effectifTotal',
            'statsEmployes',
            'repartitionGenre',
            'statsDemandes',
            'employesParService',
            'ageMoyenParService',
            'delaiTraitement',
            'joursAbsencesParCategorie'
        ));
    }
}