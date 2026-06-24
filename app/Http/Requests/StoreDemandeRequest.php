<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreDemandeRequest extends FormRequest
{
    /**
     * Déterminer si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        // Seuls les employés ordinaires (ou l'admin) ont le droit de soumettre une demande
        return Auth::check() && (Auth::user()->code_poste === 'EMPLOYE' || Auth::user()->code_poste === 'ADMIN');
    }

    /**
     * Règles de validation appliquées à la requête.
     */
    public function rules(): array
    {
        return [
            'date_debut_prevu'     => 'required|date|after_or_equal:today',
            'date_fin_prevu'       => 'required|date|after_or_equal:date_debut_prevu',
            'motif'                => 'required|string|min:10|max:500',
            'code_type_permission' => 'required|string|exists:type_permission,code_type_permission',
        ];
    }

    /**
     * Messages d'erreur personnalisés en français.
     */
    public function messages(): array
    {
        return [
            'date_debut_prevu.required'     => 'La date de début est obligatoire.',
            'date_debut_prevu.after_or_equal' => 'La date de début ne peut pas être passée.',
            'date_fin_prevu.required'       => 'La date de fin est obligatoire.',
            'date_fin_prevu.after'          => 'La date de fin doit être supérieure à la date de début.',
            'motif.required'                => 'Veuillez fournir un motif clair pour votre demande.',
            'motif.min'                     => 'Le motif doit contenir au moins 10 caractères pour être examiné.',
            'code_type_permission.exists'   => 'Le type de permission sélectionné est invalide.',
        ];
    }
}