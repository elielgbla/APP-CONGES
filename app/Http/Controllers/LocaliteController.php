<?php

namespace App\Http\Controllers;

use App\Models\Localite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LocaliteController extends Controller
{
    /**
     * Sécurisation du CRUD : Réservé à l'ADMIN
     */
    public function __construct()
    {
        if (Auth::check() && Auth::user()->code_poste !== 'ADMIN') {
            abort(403, "Action non autorisée. Réservé à l'administrateur.");
        }
    }

    /**
     * Affiche la liste des localités avec pagination.
     */
    public function index()
    {
        // Correction : Initialisation de la requête avec Localite::query() avant d'appliquer la pagination
        $localites = Localite::orderBy('libelle_localite', 'asc')->paginate(15)->withQueryString();
        
        return view('localites.index', compact('localites'));
    }

    /**
     * Affiche le formulaire de création.
     */
    public function create()
    {
        return view('localites.create');
    }

    /**
     * Enregistre une nouvelle localité.
     */
    public function store(Request $request)
    {
        $request->validate([
            'libelle_localite' => 'required|string|max:100|unique:localite,libelle_localite',
        ]);

        try {
            DB::transaction(function () use ($request) {
                Localite::create([
                    'libelle_localite' => $request->libelle_localite,
                ]);
            });

            return redirect()->route('localites.index')->with('success', 'Localité créée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur création localité : " . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'enregistrement.');
        }
    }

    /**
     * Affiche une localité spécifique.
     */
    public function show($code_localite)
    {
        $localite = Localite::findOrFail($code_localite);
        return view('localites.show', compact('localite'));
    }

    /**
     * Affiche le formulaire de modification.
     */
    public function edit($code_localite)
    {
        $localite = Localite::findOrFail($code_localite);
        return view('localites.edit', compact('localite'));
    }

    /**
     * Met à jour une localité.
     */
    public function update(Request $request, $code_localite)
    {
        $request->validate([
            'libelle_localite' => 'required|string|max:100|unique:localite,libelle_localite,' . $code_localite . ',code_localite',
        ]);

        try {
            $localite = Localite::findOrFail($code_localite);
            $localite->update(['libelle_localite' => $request->libelle_localite]);

            return redirect()->route('localites.index')->with('success', 'Localité mise à jour.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour.');
        }
    }

    /**
     * Supprime une localité.
     */
    public function destroy($code_localite)
    {
        try {
            $localite = Localite::findOrFail($code_localite);
            
            // Vérification de sécurité : empêcher la suppression si des résidences y sont liées
            if ($localite->residences()->exists()) {
                return back()->with('error', 'Impossible de supprimer cette localité : des employés y sont rattachés.');
            }

            $localite->delete();
            return redirect()->route('localites.index')->with('success', 'Localité supprimée.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression.');
        }
    }
}