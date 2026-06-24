<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use App\Models\Journalisation;
use App\Models\HistoriquePoste;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Afficher le formulaire de connexion (Login)
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Afficher le formulaire d'inscription (Register)
     */
    public function showRegister()
    {
        $genres = \App\Models\Genre::all();
        $categories = \App\Models\Categorie::all();
        $services = \App\Models\Service::all();
        $postes = \App\Models\Poste::all();
        $localites = \App\Models\Localite::all();

        return view('auth.register', compact('genres', 'categories', 'services', 'postes', 'localites'));
    }

    /**
     * Gérer la tentative de connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'matricule_employe' => 'required|string',
            'mot_de_passe'      => 'required|string',
        ]);

        // Recherche par matricule OU par email
        $employe = Employe::query()
            ->where('matricule_employe', $credentials['matricule_employe'])
            ->orWhere('email', $credentials['matricule_employe'])
            ->first();

        if ($employe && Hash::check($credentials['mot_de_passe'], $employe->mot_de_passe)) {

            // Connexion officielle dans la session de Laravel
            auth()->login($employe);

            // Obligatoire pour fixer la session sur le navigateur
            $request->session()->regenerate();

            // Journalisation
            Journalisation::create([
                'matricule_employe' => $employe->matricule_employe,
                'action'            => 'Connexion',
                'table_concernee'   => 'employe',
                'details'           => 'Connexion réussie.',
                'ip_address'        => $request->ip()
            ]);

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'matricule_employe' => 'Les identifiants sont incorrects.',
        ])->onlyInput('matricule_employe');
    }

    /**
     * Gérer l'inscription d'un nouvel employé et initialiser ses droits d'accès via l'historique
     */
    public function register(RegisterRequest $request)
    {
        // Utilisation d'une transaction DB pour s'assurer que l'employe ET son historique se créent ensemble
        DB::beginTransaction();

        try {
            // 1. Création de la fiche employé (uniquement avec les colonnes réelles de votre table employe)
            $employe = Employe::create([
                'matricule_employe'       => $request->matricule_employe,
                'nom'                     => $request->nom,
                'prenom'                  => $request->prenom,
                'email'                   => $request->email,
                'date_naissance'          => $request->date_naissance,
                'code_genre'              => $request->code_genre,
                'code_categorie'          => $request->code_categorie,
                'code_localite_naissance' => $request->code_localite_naissance,
                'mot_de_passe'            => Hash::make($request->mot_de_passe),
                'statut_compte'           => 'actif',
                'total_conge_restant'     => 30, // Allocation de base réglementaire
            ]);

            // 2. Génération du code d'historique (Exemple: HIST-XXXXX)
            $codeHistorique = 'HIST-' . strtoupper(substr(uniqid(), -8));

            // 3. Liaison immédiate du poste et du service dans historique_poste pour attribuer ses droits d'accès
            HistoriquePoste::create([
                'code_historique'   => $codeHistorique,
                'matricule_employe' => $employe->matricule_employe,
                'code_poste'        => $request->code_poste,   // Détermine s'il est admin, responsable, etc.
                'code_service'      => $request->code_service,
                'date_debut'        => Carbon::now()->toDateString(),
                'date_fin'          => null, // Reste actif tant que pas de date de fin
            ]);

            // Tracing d'audit de l'inscription
            Journalisation::create([
                'matricule_employe' => $employe->matricule_employe,
                'action'            => 'Inscription',
                'table_concernee'   => 'employe, historique_poste',
                'details'           => "Création de compte et affectation initiale au poste: {$request->code_poste}.",
                'ip_address'        => $request->ip()
            ]);

            DB::commit();

            // Connexion automatique immédiate après inscription via le guard Laravel
            auth()->login($employe);
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('success', 'Votre compte a été configuré avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => "Une erreur est survenue lors de l'inscription : " . $e->getMessage()]);
        }
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request)
    {
        $employe = auth()->user();

        if ($employe) {
            Journalisation::create([
                'matricule_employe' => $employe->matricule_employe,
                'action'            => 'Déconnexion',
                'table_concernee'   => 'employe',
                'details'           => 'L\'utilisateur s\'est déconnecté (Fermeture de session).',
                'ip_address'        => $request->ip()
            ]);
        }

        // Déconnexion officielle du Guard Laravel
        auth()->logout();

        // Nettoyage complet et invalidation de la session actuelle
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}