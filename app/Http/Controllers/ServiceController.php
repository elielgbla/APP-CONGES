<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Employe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceController extends Controller
{
    /**
     * Sécurisation du CRUD : Seul l'ADMIN gère les structures de services
     */
    public function __construct()
    {
        if (Auth::check() && Auth::user()->code_poste !== 'ADMIN') {
            abort(403, "Action non autorisée. Réservé à l'administrateur.");
        }
    }

    /**
     * INDEX : Liste des services avec barre de recherche intégrée
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $query = Service::query();

        if (!empty($search)) {
            $query->where('code_service', 'LIKE', "%{$search}%")
                  ->orWhere('libelle_service', 'LIKE', "%{$search}%");
        }

        // Récupération avec le décompte des employés pour l'affichage global
        $services = $query->withCount('employes')->orderBy('libelle_service', 'asc')->get();

        return view('services.index', compact('services', 'search'));
    }

    /**
     * CREATE : Formulaire d'ajout
     */
    public function create(): View
    {
        return view('services.create');
    }

    /**
     * STORE : Enregistrement
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code_service' => 'required|string|max:50|unique:service,code_service',
            'libelle_service' => 'required|string|max:150'
        ]);

        $data = $request->all();
        $data['code_service'] = strtoupper($request->input('code_service'));

        Service::create($data);

        return redirect()->route('services.index')->with('success', 'Service créé avec succès.');
    }

    /**
     * SHOW : Fiche du service avec Chef, liste du personnel et option d'impression
     */
    public function show(string $code_service): View
    {
        // On charge le service avec ses employés et leur poste associé
        $service = Service::with(['employes.poste'])->findOrFail($code_service);

        // Identification du Chef de Service : On cherche l'employé de ce service 
        // dont le code_poste ou le rôle correspond au superviseur/responsable (N1/N2)
        $chefService = $service->employes()
            ->whereIn('code_poste', ['RESPONSABLE', 'CHEF_SERVICE', 'DIRECTEUR'])
            ->first();

        return view('services.show', compact('service', 'chefService'));
    }

    /**
     * EDIT : Formulaire de modification
     */
    public function edit(string $code_service): View
    {
        $service = Service::findOrFail($code_service);
        return view('services.edit', compact('service'));
    }

    /**
     * UPDATE : Mise à jour
     */
    public function update(Request $request, string $code_service): RedirectResponse
    {
        $service = Service::findOrFail($code_service);

        $request->validate([
            'libelle_service' => 'required|string|max:150'
        ]);

        $service->update($request->only(['libelle_service']));

        return redirect()->route('services.index')->with('success', 'Service mis à jour avec succès.');
    }

    /**
     * DESTROY : Suppression sécurisée
     */
    public function destroy(string $code_service): RedirectResponse
    {
        $service = Service::findOrFail($code_service);

        // Sécurité : On ne dissout pas un service s'il contient encore des agents rattachés
        if ($service->employes()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce service car des employés y sont encore affectés.');
        }

        $service->delete();

        return redirect()->route('services.index')->with('success', 'Service supprimé avec succès.');
    }

    /**
     * IMPRIMER FICHE SERVICE : Génère la vue épurée pour l'impression de la fiche
     */
    public function imprimerFiche(string $code_service): View
    {
        $service = Service::with(['employes.poste'])->findOrFail($code_service);
        
        $chefService = $service->employes()
            ->whereIn('code_poste', ['RESPONSABLE', 'CHEF_SERVICE', 'DIRECTEUR'])
            ->first();

        return view('services.imprimer_fiche', compact('service', 'chefService'));
    }
}