<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use App\Models\Journalisation;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

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
        $localites = \App\Models\Localite::all();

        return view('auth.register', compact('genres', 'categories', 'localites'));
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
            if (strtolower((string) $employe->statut_compte) !== 'actif') {
                return back()->withErrors([
                    'matricule_employe' => strtolower((string) $employe->statut_compte) === 'suspendu'
                        ? 'Ce compte est suspendu. Veuillez contacter un administrateur.'
                        : 'Votre inscription est en attente d’activation par un administrateur.',
                ])->onlyInput('matricule_employe');
            }

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
     * Gérer l'inscription d'un nouvel employé en attente d'activation administrative.
     */
    public function register(RegisterRequest $request)
    {
        DB::transaction(function () use ($request) {
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
                'statut_compte'           => 'inactif',
                'total_conge_restant'     => 30, // Allocation de base réglementaire
            ]);

            // Tracing d'audit de l'inscription
            Journalisation::create([
                'matricule_employe' => $employe->matricule_employe,
                'action'            => 'Inscription',
                'table_concernee'   => 'employe',
                'details'           => 'Création du compte en attente d’activation administrative.',
                'ip_address'        => $request->ip()
            ]);
        });

        return redirect()->route('login')->with('success', 'Votre inscription est enregistrée. Un administrateur doit activer votre compte avant votre première connexion.');
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