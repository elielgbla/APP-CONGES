<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Employe;
use App\Models\Journalisation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * R - READ : Afficher le formulaire de modification du mot de passe
     */
    public function edit()
    {
        return view('auth.passwords.edit');
    }

    /**
     * U - UPDATE : Traiter la mise à jour du mot de passe (Profil connecté)
     */
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::min(6)->letters(), 'confirmed'],
        ], [
            'current_password.required' => 'Le mot de passe actuel est obligatoire.',
            'password.required' => 'Le nouveau mot de passe est obligatoire.',
            'password.min' => 'Le nouveau mot de passe doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas.',
        ]);

        /** @var \App\Models\Employe $employe */
        $employe = Auth::user();

        if (!$employe) {
            return redirect()->route('login');
        }

        if (!Hash::check($request->current_password, $employe->mot_de_passe)) {
            return back()->withErrors([
                'current_password' => 'Le mot de passe actuel fourni ne correspond pas à nos enregistrements.',
            ]);
        }

        $employe->update([
            'mot_de_passe' => Hash::make($request->password),
        ]);

        Journalisation::create([
            'matricule_employe' => $employe->matricule_employe,
            'action' => 'Changement MDP',
            'table_concernee' => 'employe',
            'details' => 'Mise à jour réussie du mot de passe depuis le module de sécurité.',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Votre mot de passe a été modifié avec succès.');
    }

    /**
     * Opération CRUD alternative : Réinitialisation forcée par un Administrateur
     */
    public function resetByAdmin(Request $request, string $matricule)
    {
        if (!Auth::check() || !in_array(Auth::user()->code_poste, ['RH', 'ADMIN'])) {
            abort(403, 'Action non autorisée.');
        }

        $employe = Employe::where('matricule_employe', $matricule)->first();

        if (!$employe) {
            return back()->with('error', 'Collaborateur introuvable.');
        }

        $motDePasseParDefaut = '@m0tDep@sse';

        $employe->update([
            'mot_de_passe' => Hash::make($motDePasseParDefaut),
        ]);

        Journalisation::create([
            'matricule_employe' => Auth::user()->matricule_employe,
            'action' => 'Réinitialisation Admin',
            'table_concernee' => 'employe',
            'details' => "Réinitialisation du mot de passe de l'employé au matricule : {$matricule}.",
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Le mot de passe de l'employé a été réinitialisé avec succès sur : {$motDePasseParDefaut}");
    }
}