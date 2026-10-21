<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EmployeController extends Controller implements HasMiddleware
{
    /**
     * Définition des middlewares de contrôle d'accès
     */
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!Auth::check() || !in_array(Auth::user()->code_poste, ['RH', 'ADMIN'])) {
                    abort(403, 'Action non autorisée. Accès réservé aux gestionnaires RH et Administrateurs.');
                }
                return $next($request);
            }),
        ];
    }

    /**
     * Liste des employés avec filtres et recherche
     */
    public function index(Request $request)
    {
        $query = Employe::query();

        // Recherche par mot-clé
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('matricule_employe', 'LIKE', "%{$search}%")
                  ->orWhere('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('code_poste', 'LIKE', "%{$search}%");
            });
        }

        // Filtre par statut de compte
        if ($request->filled('statut')) {
            $query->where('statut_compte', $request->input('statut'));
        }

        // Pagination fluide en conservant les filtres de recherche dans l'URL
        $employes = $query->orderBy('nom', 'asc')->paginate(10)->withQueryString();

        return view('employes.index', compact('employes'));
    }

    /**
     * Formulaire de création d'un nouvel employé
     */
    public function create()
    {
        // Récupération des tables de référence (Singulier respecté selon ta BD)
        $postes = DB::table('poste')->orderBy('libelle_poste', 'asc')->get();
        $services = DB::table('service')->orderBy('libelle_service', 'asc')->get();
        $categories = DB::table('categorie')->orderBy('libelle_categorie', 'asc')->get();
        $genres = DB::table('genre')->orderBy('libelle_genre', 'asc')->get();
        $localites = DB::table('localite')->orderBy('libelle_localite', 'asc')->get();

        return view('employes.create', compact('genres', 'postes', 'services', 'categories', 'localites'));
    }

    /**
     * Enregistrement de l'employé
     */
    public function store(Request $request)
    {
        $request->validate([
            'matricule_employe' => 'required|string|max:20|unique:employe,matricule_employe',
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:70',
            'date_naissance' => 'nullable|date',
            'code_localite_naissance' => 'nullable|string|max:10',
            'mot_de_passe' => 'required|string|min:6',
            'statut_compte' => 'required|in:actif,inactif,suspendu',
            'email' => 'nullable|email|max:100|unique:employe,email',
            'total_conge_restant' => 'required|integer|min:0',
            'code_genre' => 'required|string|max:10',
            'code_categorie' => 'required|string|max:10',

        ]);

        $data = $request->except(['photo']);
        $data['mot_de_passe'] = Hash::make($request->input('mot_de_passe'));

        // Traitement sécurisé du téléversement de la photo
        if ($request->hasFile('photo')) {
            $filename = 'photo_' . $request->input('matricule_employe') . '_' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $path = $request->file('photo')->storeAs('photos_employes', $filename, 'public');
            $data['photo'] = $path;
        }

        Employe::create($data);

        return redirect()->route('employes.index')->with('success', 'Employé créé avec succès.');
    }

    /**
     * Affichage de la fiche individuelle d'un employé
     */
  public function show($matricule)
    {
        $employe = Employe::with([
            'genre',
            'categorie',
            'contacts.typeContact',
            'residences.localite',
            'historiquePostes.poste',
            'historiquePostes.service',
            'demandes.typePermission',
            'planningsJournaliers',
            'journalisations' // Historique des actions (logs)
        ])->where('matricule_employe', $matricule)->firstOrFail();

        // Récupération du poste et service actuel via le dernier historique actif (sans date_fin)
        $historiqueActuel = $employe->historiquePostes->where('date_fin', null)->first() 
            ?? $employe->historiquePostes->sortByDesc('date_debut')->first();

        return view('employes.show', compact('employe', 'historiqueActuel'));
    }
    /**
     * Formulaire de modification d'un employé
     */
    public function edit(string $matricule_employe)
    {
        $employe = Employe::where('matricule_employe', $matricule_employe)->firstOrFail();

        $categories = DB::table('categorie')->orderBy('libelle_categorie', 'asc')->get();
        $genres = DB::table('genre')->orderBy('libelle_genre', 'asc')->get();
        $localites = DB::table('localite')->orderBy('libelle_localite', 'asc')->get();
        
        return view('employes.edit', compact('employe', 'genres', 'categories', 'localites'));
    }

    /**
     * Mise à jour de la fiche de l'employé
     */
    public function update(Request $request, string $matricule_employe)
    {   
        $localites = DB::table('localite')->pluck('code_localite')->toArray();
        $employe = Employe::where('matricule_employe', $matricule_employe)->firstOrFail();

        $request->validate([
    'nom' => 'required|string|max:50',
    'prenom' => 'required|string|max:70',
    'date_naissance' => 'nullable|date',
    'code_localite_naissance' => 'nullable|string|max:10|exists:localite,code_localite',
    'mot_de_passe' => 'nullable|string|min:6',
    'statut_compte' => 'required|in:actif,inactif,suspendu',
    'email' => 'nullable|email|max:100|unique:employe,email,'.$employe->matricule_employe.',matricule_employe',

    'total_conge_restant' => 'required|integer|min:0',
    'code_genre' => 'required|string|max:10|exists:genre,code_genre',
    'code_categorie' => 'required|string|max:10|exists:categorie,code_categorie',
]);

        $data = $request->except(['photo', 'mot_de_passe']);

        // Hachage du mot de passe uniquement s'il est renseigné
        if ($request->filled('mot_de_passe')) {
            $data['mot_de_passe'] = Hash::make($request->input('mot_de_passe'));
        }

        // Remplacement de la photo
        if ($request->hasFile('photo')) {
            if ($employe->photo) {
                Storage::disk('public')->delete($employe->photo);
            }

            $filename = 'photo_' . $employe->matricule_employe . '_' . time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $path = $request->file('photo')->storeAs('photos_employes', $filename, 'public');
            $data['photo'] = $path;
        }

        $employe->update($data);

        return redirect()->route('employes.index')->with('success', 'Fiche employé mise à jour avec succès.');
    }

    /**
     * Retrait ou désactivation d'un employé de l'effectif
     */
    public function destroy(string $matricule_employe)
    {
        $employe = Employe::where('matricule_employe', $matricule_employe)->firstOrFail();

        // Mesure de sécurité : Empêcher le suicide de compte de session
        if (Auth::user()->matricule_employe === $employe->matricule_employe) {
            return redirect()->route('employes.index')->with('error', 'Action impossible : vous ne pouvez pas supprimer votre propre compte depuis ce module.');
        }

        // Suppression physique du fichier image lié si existant
        if ($employe->photo) {
            Storage::disk('public')->delete($employe->photo);
        }

        $employe->delete();

        return redirect()->route('employes.index')->with('success', 'L\'employé a été retiré de l\'effectif.');
    }
}