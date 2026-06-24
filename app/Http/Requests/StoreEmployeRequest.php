<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'matricule_employe' => 'required|string|max:20|unique:employe,matricule_employe',
            'nom' => 'required|string|max:50',
            'prenom' => 'required|string|max:70',
            'email' => 'required|email|max:100|unique:employe,email',
            'mot_de_passe' => 'required|string|min:6|confirmed',
            'code_genre' => 'required|string|exists:genre,code_genre',
            'code_categorie' => 'required|string|exists:categorie,code_categorie',
            'code_poste' => 'required|string|exists:poste,code_poste',
            'code_service' => 'required|string|exists:service,code_service',
            'date_naissance' => 'nullable|date',
            'code_localite_naissance' => 'nullable|string|exists:localite,code_localite',
            'code_residence' => 'nullable|string|exists:residence,code_residence',
        ];
    }
}