<?php

namespace App\Http\Controllers;

use App\Models\Planning;
use App\Models\Employe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PlanningController extends Controller
{
    /**
     * Sécurisation de l'accès : Restreint à l'ADMIN uniquement
     */
public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                $user = Auth::user();

                // Vérification stricte du rôle/poste de l'utilisateur connecté
                if (!$user || !in_array($user->code_poste, ['ADMIN', 'RH'])) {
                    abort(403, "Action non autorisée : Droits d'accès insuffisants.");
                }

                return $next($request);
            }),
        ];
    }

    /**
     * INDEX : Liste uniquement les agents ayant au moins un programme
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        // Filtre les employés qui possèdent au moins une ligne de planning
        $query = Employe::whereHas('planningsJournaliers');

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('matricule_employe', 'LIKE', "%{$search}%");
            });
        }

        // On injecte le nombre de programmes par agent pour l'affichage du badge
        $employes = $query->withCount('planningsJournaliers')
                          ->orderBy('nom', 'asc')
                          ->get();

        return view('plannings.index', compact('employes', 'search'));
    }

    /**
     * CREATE : Formulaire d'ajout d'un créneau
     */
    public function create(Request $request): View
    {
        $employes = Employe::orderBy('nom', 'asc')->get();
        $matriculePreselectionne = $request->query('matricule');
        
        $joursSemaine = $this->getJoursSemaine();

        return view('plannings.create', compact('employes', 'matriculePreselectionne', 'joursSemaine'));
    }

    /**
     * STORE : Enregistrement d'un nouveau créneau horaire avec jours de repos
     */
  public function store(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'matricule_employe' => 'required|string|exists:employe,matricule_employe', // Note : ajuste 'employes' ou 'employe' selon le nom exact de ta table
        'date_debut'        => 'required|date',
        'heure_debut'       => 'required',
        'heure_fin'         => 'required',
        'jour_repos_1'      => 'required|integer|between:1,7',
        'jour_repos_2'      => 'nullable|integer|between:1,7|different:jour_repos_1',
    ], [
        'jour_repos_2.different' => 'Le deuxième jour de repos doit impérativement être différent du premier.',
    ]);

    // 1. LOGIQUE DE CLÔTURE DE L'ANCIEN PLANNING (Remplace le Trigger)
    $planningPrecedent = Planning::where('matricule_employe', $request->matricule_employe)
        ->whereNull('date_fin')
        ->first();

    if ($planningPrecedent) {
        // Calcule la veille de la nouvelle date de début (ex: 19/12/2026 -> 18/12/2026)
        $nouvelleDateDebut = \Carbon\Carbon::parse($request->date_debut);
        $dateFinPrecedent = $nouvelleDateDebut->subDay()->format('Y-m-d');

        // Met à jour l'ancien planning avec sa date de fin
        $planningPrecedent->update([
            'date_fin' => $dateFinPrecedent
        ]);
    }

    // 2. Insertion sécurisée en utilisant les données validées
    Planning::create($validated);

    return redirect()->route('plannings.show', $request->matricule_employe)
        ->with('success', 'Nouveau créneau horaire ajouté avec succès et planning précédent clôturé.');
}

    /**
     * SHOW : La fiche regroupant TOUS les programmes de l'agent avec calculs de calendrier
     */
    public function show(string $id): View
    {
        // Récupération de l'employé avec ses relations de base
        $employe = Employe::with(['poste', 'service'])
            ->where('matricule_employe', '=', $id)
            ->firstOrFail();

        // Récupération de toute sa grille horaire triée chronologiquement
        $programmes = Planning::where('matricule_employe', '=', $id)
            ->orderBy('date_debut', 'asc')
            ->orderBy('heure_debut', 'asc')
            ->get();

        $joursSemaine = $this->getJoursSemaine();

        // Injection algorithmique des données de statistiques pour la fenêtre flottante (Modal)
        foreach ($programmes as $planning) {
            $debut = Carbon::parse($planning->date_debut);
            $fin = Carbon::parse($planning->date_fin);
            
            $periode = CarbonPeriod::create($debut, $fin);
            
            $joursTravail = 0;
            $joursRepos = 0;
            $reposIds = array_filter([$planning->jour_repos_1, $planning->jour_repos_2]);

            foreach ($periode as $date) {
                // date->format('N') renvoie 1 (Lundi) à 7 (Dimanche)
                if (in_array($date->format('N'), $reposIds)) {
                    $joursRepos++;
                } else {
                    $joursTravail++;
                }
            }

            // Calcul du volume horaire par jour (Prise en charge des horaires de nuit ex: 15h à 00h)
            $heureDebut = Carbon::parse($planning->heure_debut);
            $heureFin = Carbon::parse($planning->heure_fin);
            
            if ($heureFin->lessThan($heureDebut)) {
                $heuresParJour = $heureDebut->diffInHours($heureFin->addDay());
            } else {
                $heuresParJour = $heureDebut->diffInHours($heureFin);
            }

            // Propriétés dynamiques injectées à chaud dans la collection
            $planning->total_jours_travail = $joursTravail;
            $planning->total_jours_repos   = $joursRepos;
            $planning->total_heures_travail = $joursTravail * $heuresParJour;
        }

        return view('plannings.show', compact('employe', 'programmes', 'joursSemaine'));
    }

    /**
     * EDIT : Modification d'un créneau horaire spécifique
     */
    public function edit(string $code_planning): View
    {
        $planning = Planning::findOrFail($code_planning);
        $employes = Employe::orderBy('nom', 'asc')->get();
        $joursSemaine = $this->getJoursSemaine();

        return view('plannings.edit', compact('planning', 'employes', 'joursSemaine'));
    }

    /**
     * UPDATE : Sauvegarde des modifications du créneau avec gestion des exclusions de repos
     */
    public function update(Request $request, string $code_planning): RedirectResponse
    {
        $planning = Planning::findOrFail($code_planning);

        $request->validate([
            'matricule_employe' => 'required|string|exists:employe,matricule_employe',
            'date_debut'        => 'required|date',
            'date_fin'          => 'required|date|after_or_equal:date_debut',
            'heure_debut'       => 'required',
            'heure_fin'         => 'required',
            'jour_repos_1'      => 'required|integer|between:1,7',
            'jour_repos_2'      => 'required|integer|between:1,7|different:jour_repos_1',
        ], [
            'jour_repos_2.different' => 'Le deuxième jour de repos doit impérativement être différent du premier.',
        ]);

        if ($request->date_debut === $request->date_fin) {
            if (strtotime($request->heure_fin) <= strtotime($request->heure_debut)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "L'heure de fin doit être strictement supérieure à l'heure de début.");
            }
        }

        $planning->update($request->all());

        return redirect()->route('plannings.show', $request->matricule_employe)
            ->with('success', 'Créneau horaire mis à jour avec succès.');
    }

    /**
     * DESTROY : Retrait d'une ligne de planning
     */
    public function destroy(string $code_planning): RedirectResponse
    {
        $planning = Planning::findOrFail($code_planning);
        $matriculeEmploye = $planning->matricule_employe;

        $planning->delete();

        return redirect()->route('plannings.show', $matriculeEmploye)
            ->with('success', 'Le créneau horaire a été retiré de la fiche de l’agent.');
    }

    /**
     * Récupère la liste des jours indexés ISO-8601
     */
    private function getJoursSemaine(): array
    {
        return [
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
            7 => 'Dimanche',
        ];
    }
}