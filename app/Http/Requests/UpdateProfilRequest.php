<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateProfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $matricule = Auth::user()->matricule_employe;

        return [
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:150',
            'email'          => 'required|email|max:150|unique:employe,email,' . $matricule . ',matricule_employe',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2Mo
            'ancien_mdp'     => 'nullable|string|required_with:nouveau_mdp',
            'nouveau_mdp'    => 'nullable|string|min:6|confirmed|different:ancien_mdp',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique'         => 'Cette adresse email est déjà utilisée par un autre collaborateur.',
            'photo.image'          => 'Le fichier doit être une image valide.',
            'photo.max'            => 'La photo ne doit pas dépasser 2 Mo.',
            'nouveau_mdp.min'      => 'Le nouveau mot de passe doit contenir au moins 6 caractères.',
            'nouveau_mdp.confirmed'=> 'La confirmation du nouveau mot de passe ne correspond pas.',
            'nouveau_mdp.different'=> 'Le nouveau mot de passe doit être différent de l\'ancien.',
            'ancien_mdp.required_with' => 'Vous devez saisir votre ancien mot de passe pour le modifier.',
        ];
    }
}