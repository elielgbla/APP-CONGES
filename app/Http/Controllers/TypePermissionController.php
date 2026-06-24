<?php

namespace App\Http\Controllers;

use App\Models\TypePermission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TypePermissionController extends Controller
{
    /**
     * Constructeur pour sécuriser le CRUD (Uniquement ADMIN autorisé)
     */
    public function __construct()
    {
        if (Auth::check() && Auth::user()->code_poste !== 'ADMIN') {
            abort(403, "Action non autorisée. Réservé à l'administrateur.");
        }
    }

    /**
     * INDEX : Liste des types de permissions
     */
    public function index(): View
    {
        $types = TypePermission::all();
        return view('type_permissions.index', compact('types'));
    }

    /**
     * CREATE : Formulaire de création
     */
    public function create(): View
    {
        return view('type_permissions.create');
    }

    /**
     * STORE : Enregistrement en base de données
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code_type_permission' => 'required|string|max:50|unique:type_permission,code_type_permission',
            'libelle_type_permission' => 'required|string|max:150',
           
        ]);

        $data = $request->all();
        $data['code_type_permission'] = strtoupper($request->input('code_type_permission'));

        TypePermission::create($data);

        // CORRIGÉ : Utilisation du tiret pour correspondre à web.php
        return redirect()->route('type-permissions.index')->with('success', 'Type de permission créé avec succès.');
    }

    /**
     * SHOW : Fiche de consultation unique
     */
    public function show(string $code_type_permission): View
    {
        // CORRIGÉ : utilisation de findOrFail ou de la structure explicite pour Intelephense
        $type = TypePermission::findOrFail($code_type_permission);

        return view('type_permissions.show', compact('type'));
    }

    /**
     * EDIT : Formulaire d'édition
     */
    public function edit(string $code_type_permission): View
    {
        // CORRIGÉ : Évite le conflit d'arguments Intelephense
        $type = TypePermission::findOrFail($code_type_permission);

        return view('type_permissions.edit', compact('type'));
    }

    /**
     * UPDATE : Mise à jour des données
     */
    public function update(Request $request, string $code_type_permission): RedirectResponse
    {
        $type = TypePermission::findOrFail($code_type_permission);

        $request->validate([
            'libelle_type_permission' => 'required|string|max:150',

        ]);

        $type->update($request->only(['libelle_type_permission']));

        // CORRIGÉ : Utilisation du tiret pour correspondre à web.php
        return redirect()->route('type-permissions.index')->with('success', 'Type de permission mis à jour.');
    }

    /**
     * DESTROY : Suppression d'une référence
     */
    public function destroy(string $code_type_permission): RedirectResponse
    {
        $type = TypePermission::findOrFail($code_type_permission);

        if ($type->demandes()->count() > 0) {
            return redirect()->back()->with('error', 'Impossible de supprimer ce type car il est actuellement rattaché à des dossiers de demandes actifs.');
        }

        $type->delete();

        // CORRIGÉ : Utilisation du tiret pour correspondre à web.php
        return redirect()->route('type-permissions.index')->with('success', 'Type de permission supprimé avec succès.');
    }
}