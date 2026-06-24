<?php

namespace App\Http\Controllers;

use App\Models\Poste;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PosteController extends Controller
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
     * INDEX : Liste avec barre de recherche
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $query = Poste::query();

        if (!empty($search)) {
            $query->where('code_poste', 'LIKE', "%{$search}%")
                  ->orWhere('libelle_poste', 'LIKE', "%{$search}%");
        }

        $postes = $query->withCount('employes')->orderBy('libelle_poste', 'asc')->get();

        return view('postes.index', compact('postes', 'search'));
    }

    /**
     * CREATE
     */
    public function create(): View
    {
        return view('postes.create');
    }

    /**
     * STORE
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code_poste' => 'required|string|max:50|unique:poste,code_poste',
            'libelle_poste' => 'required|string|max:150'
        ]);

        $data = $request->all();
        $data['code_poste'] = strtoupper($request->input('code_poste'));

        Poste::create($data);

        return redirect()->route('postes.index')->with('success', 'Poste de travail créé avec succès.');
    }

    /**
     * SHOW : Fiche du poste et liste complète des agents associés
     */
    public function show(string $code_poste): View
    {
        $poste = Poste::with(['employes.service'])->findOrFail($code_poste);
        return view('postes.show', compact('poste'));
    }

    /**
     * EDIT
     */
    public function edit(string $code_poste): View
    {
        $poste = Poste::findOrFail($code_poste);
        return view('postes.edit', compact('poste'));
    }

    /**
     * UPDATE
     */
    public function update(Request $request, string $code_poste): RedirectResponse
    {
        $poste = Poste::findOrFail($code_poste);

        $request->validate([
            'libelle_poste' => 'required|string|max:150'
        ]);

        $poste->update($request->only(['libelle_poste']));

        return redirect()->route('postes.index')->with('success', 'Poste de travail mis à jour.');
    }

    /**
     * DESTROY
     */
    public function destroy(string $code_poste): RedirectResponse
    {
        $poste = Poste::findOrFail($code_poste);

        // Sécurité : Interdiction de supprimer si des employés possèdent ce code_poste
        if ($poste->employes()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce poste car des employés y sont actuellement rattachés.');
        }

        $poste->delete();

        return redirect()->route('postes.index')->with('success', 'Poste supprimé avec succès.');
    }

    /**
     * IMPRIMER FICHE POSTE
     */
    public function imprimerFiche(string $code_poste): View
    {
        $poste = Poste::with(['employes.service'])->findOrFail($code_poste);
        return view('postes.imprimer_fiche', compact('poste'));
    }
}
