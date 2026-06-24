<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Accessible publiquement pour les invités
    }

    public function rules(): array
    {
        return [
            'matricule_employe'       => 'required|string|unique:employe,matricule_employe|max:50',
            'nom'                     => 'required|string|max:100',
            'prenom'                  => 'required|string|max:150',
            'email'                   => 'required|email|unique:employe,email|max:150',
            'date_naissance'          => 'nullable|date|before:today',
            'code_genre'              => 'required|exists:genre,code_genre',
            'code_service'            => 'required|exists:service,code_service',
            'code_poste'              => 'required|exists:poste,code_poste',
            'code_categorie'          => 'required|exists:categorie,code_categorie',
            'code_residence'          => 'nullable|exists:residence,code_residence',
            'code_localite_naissance' => 'nullable|exists:localite,code_localite',
            'mot_de_passe'            => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'matricule_employe.unique' => 'Ce matricule est déjà attribué à un collaborateur.',
            'email.unique'             => 'Cette adresse email est déjà enregistrée.',
            'mot_de_passe.confirmed'   => 'La confirmation du mot de passe ne correspond pas.',
            'mot_de_passe.min'         => 'Le mot de passe doit mesurer au moins 6 caractères pour des raisons de sécurité.',
            'exists'                   => 'La référence sélectionnée est introuvable dans nos bases de paramétrage.',
        ];
    }
}