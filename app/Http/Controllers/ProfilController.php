<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Journalisation;
use App\Http\Requests\UpdateProfilRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    // LE CONSTRUCTEUR EST NETTOYÉ : Plus de $this->middleware() ici pour Laravel 11.

    /**
     * R - READ : Afficher le profil de l'employé connecté
     */
    public function index()
{
    // On charge les données valides de l'employé ainsi que son poste et service actuels via son historique
    $user = Employe::query()
        ->with([
            'genre', 
            'categorie', 
            'localiteNaissance',
            'affectationActuelle.poste',   // Charge le poste lié à l'affectation active
            'affectationActuelle.service'  // Charge le service lié à l'affectation active
        ])
        ->findOrFail(auth()->user()->matricule_employe);

    return view('profil.index', compact('user'));
}

    /**
     * U - UPDATE : Mettre à jour les informations du profil et/ou le mot de passe
     */
    public function update(UpdateProfilRequest $request)
    {
        /** @var \App\Models\Employe $employe */
        $employe = Employe::find(Auth::user()->matricule_employe);

        // 1. Gestion de la mise à jour des informations de base
        $employe->nom = $request->nom;
        $employe->prenom = $request->prenom;
        $employe->email = $request->email;

        $detailsLog = "Mise à jour des informations personnelles.";

        // 2. Gestion de l'UPLOAD de la photo de profil
        if ($request->hasFile('photo')) {
            // Supprimer l'ancienne photo si elle existe pour ne pas encombrer le serveur
            if ($employe->photo && Storage::disk('public')->exists($employe->photo)) {
                Storage::disk('public')->delete($employe->photo);
            }

            // Stockage de la nouvelle photo dans le dossier public/photos_profil
            $path = $request->file('photo')->store('photos_profil', 'public');
            $employe->photo = $path;
            $detailsLog .= " Changement de la photo de profil.";
        }

        // 3. Gestion de la modification du Mot de Passe
        if ($request->filled('nouveau_mdp')) {
            // NOTE SÉCURITÉ : On vérifie si ton modèle d'authentification utilise 'password' ou 'mot_de_passe'
            $currentPasswordHash = $employe->password ?? $employe->mot_de_passe;

            // Vérification de la validité de l'ancien mot de passe
            if (!Hash::check($request->ancien_mdp, $currentPasswordHash)) {
                return back()->withErrors(['ancien_mdp' => 'L\'ancien mot de passe saisi est incorrect.']);
            }

            // Chiffrement et sauvegarde du nouveau mot de passe (on remplit les deux au cas où)
            $hashedPassword = Hash::make($request->nouveau_mdp);
            if (isset($employe->mot_de_passe)) { $employe->mot_de_passe = $hashedPassword; }
            $employe->password = $hashedPassword;

            $detailsLog .= " Modification sécurisée du mot de passe.";
        }

        // Sauvegarde dans la base de données MySQL via WAMP
        $employe->save();

        // 4. Traçabilité (Journalisation d'Audit)
        Journalisation::create([
            'matricule_employe' => $employe->matricule_employe,
            'action'            => 'Mise à jour Profil',
            'table_concernee'   => 'employe',
            'details'           => $detailsLog,
            'ip_address'        => $request->ip()
        ]);

        return redirect()->route('profil.index')->with('success', 'Votre profil a été mis à jour avec succès.');
    }

    /**
     * D - DELETE : Désactiver le compte
     */
    public function destroy(Request $request)
    {
        /** @var \App\Models\Employe $employe */
        $employe = Employe::find(Auth::user()->matricule_employe);

        // Clôture du compte
        $employe->statut_compte = 'inactif';
        $employe->save();

        // Tracing
        Journalisation::create([
            'matricule_employe' => $employe->matricule_employe,
            'action'            => 'Désactivation Compte',
            'table_concernee'   => 'employe',
            'details'           => 'L utilisateur a désactivé son compte de l application.',
            'ip_address'        => $request->ip()
        ]);

        // Déconnexion forcée de la session
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Votre compte a été désactivé.');
    }
}