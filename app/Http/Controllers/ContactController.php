<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Employe;
use App\Models\TypeContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function __construct()
    {
        if (Auth::check() && Auth::user()->code_poste !== 'ADMIN') {
            abort(403, "Action non autorisée.");
        }
    }

    /**
     * INDEX : Liste simple de tous les enregistrements de contacts
     */
   /**
     * INDEX : Liste des contacts regroupés par Employé
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        
        // On part de l'Employe pour n'avoir qu'une ligne par agent
        $query = Employe::whereHas('contacts')->with(['contacts.type', 'poste']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('matricule_employe', 'LIKE', "%{$search}%")
                  ->orWhereHas('contacts', function ($c) use ($search) {
                      $c->where('numero_contact', 'LIKE', "%{$search}%");
                  });
            });
        }

        $employes = $query->orderBy('nom', 'asc')->get();

        return view('contacts.index', compact('employes', 'search'));
    }

    public function create(): View
    {
        $employes = Employe::orderBy('nom', 'asc')->get();
        $types = TypeContact::orderBy('libelle_type_contact', 'asc')->get();

        return view('contacts.create', compact('employes', 'types'));
    }

    /**
     * STORE : Multi-enregistrement selon les cases cochées
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'numero_contact' => 'required|string|max:20',
            'matricule_employe' => 'required|string|exists:employe,matricule_employe',
            'types' => 'required|array|min:1', 
            'types.*' => 'string|exists:type_contact,code_type_contact'
        ]);

        $typesCoches = $request->input('types');

        // Utilisation d'une transaction pour s'assurer que soit tout s'enregistre, soit rien
        DB::transaction(function () use ($request, $typesCoches) {
            foreach ($typesCoches as $unCodeType) {
                // Un create() distinct par case cochée
                Contact::create([
                    'numero_contact' => $request->input('numero_contact'),
                    'matricule_employe' => $request->input('matricule_employe'),
                    'code_type_contact' => $unCodeType,
                ]);
            }
        });

        return redirect()->route('contacts.index')->with('success', 'Le(s) contact(s) ont été enregistrés avec succès.');
    }

    public function edit(string $code_contact): View
    {
        $contact = Contact::findOrFail($code_contact);
        $employes = Employe::orderBy('nom', 'asc')->get();
        $types = TypeContact::orderBy('libelle_type_contact', 'asc')->get();

        return view('contacts.edit', compact('contact', 'employes', 'types'));
    }

    public function show(string $code_contact): View
{
    // 1. On trouve le contact de départ
    $initialContact = Contact::findOrFail($code_contact);
    
    // 2. On récupère TOUTES les lignes de contacts du même employé avec leurs types respectifs
    $allContactsAgent = Contact::with('type')
        ->where('matricule_employe', $initialContact->matricule_employe)
        ->get();

    $employe = Employe::with('poste')->where('matricule_employe', $initialContact->matricule_employe)->first();

    return view('contacts.show', compact('employe', 'allContactsAgent'));
}

    /**
     * UPDATE : Modifie l'usage spécifique de la ligne sélectionnée
     */
    public function update(Request $request, string $code_contact): RedirectResponse
    {
        $contact = Contact::findOrFail($code_contact);

        $request->validate([
            'numero_contact' => 'required|string|max:20',
            'matricule_employe' => 'required|string|exists:employe,matricule_employe',
            'code_type_contact' => 'required|string|exists:type_contact,code_type_contact'
        ]);

        $contact->update($request->only(['numero_contact', 'matricule_employe', 'code_type_contact']));

        return redirect()->route('contacts.index')->with('success', 'Coordonnée mise à jour.');
    }

    public function destroy(string $code_contact): RedirectResponse
    {
        $contact = Contact::findOrFail($code_contact);
        $contact->delete();

        return redirect()->route('contacts.index')->with('success', 'Ligne de contact supprimée.');
    }
}