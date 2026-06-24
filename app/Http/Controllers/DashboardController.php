<?php

namespace App\Http\Controllers;

use App\Models\Demande;
use App\Models\Employe;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. On charge l'utilisateur connecté en utilisant son matricule lié au compte Auth
        $user = Employe::with(['historiquePostes' => function ($q) {
            $q->whereNull('date_fin')->with('poste');
        }])->where('matricule_employe', Auth::user()->matricule_employe)->first();

        if (!$user) {
            abort(404, "Profil employé introuvable.");
        }

        // On extrait proprement le code_service du Responsable connecté depuis son historique actif
        $historiqueActuelValideur = $user->historiquePostes->first();
        $userService = $historiqueActuelValideur ? $historiqueActuelValideur->code_service : null;

        $stats = [
            'mes_demandes_compte' => 0,
            'conge_restant' => $user->total_conge_restant ?? 0,
            'en_attente_validation' => 0,
            'total_employes' => 0,
        ];

        // 2. Nombre de demandes de l'employé connecté
        $stats['mes_demandes_compte'] = Demande::where('matricule_employe', $user->matricule_employe)->count();

        // 3. Gestion du circuit des 4 niveaux de validation
        $historiqueVisasRoles = ['RESP_SERVI', 'RH', 'DIR_EXPLOI', 'DIR_GENERA'];

        if (in_array($user->code_poste, $historiqueVisasRoles) || $user->code_poste === 'ADMIN') {
            $queryValidation = Demande::query();

            if ($user->code_poste === 'RESP_SERVI') {
                // Le responsable ne voit que les demandes N1 de SON propre service
                $queryValidation->where('statut_demande', 'en_attente_n1')
                    ->whereHas('employe.historiquePostes', function ($q) use ($userService) {
                        $q->where('historique_poste.code_service', $userService)
                          ->whereNull('historique_poste.date_fin');
                    });
            } elseif ($user->code_poste === 'RH') {
                $queryValidation->where('statut_demande', 'en_attente_n2');
            } elseif ($user->code_poste === 'DIR_EXPLOI') {
                $queryValidation->where('statut_demande', 'en_attente_n3');
            } elseif ($user->code_poste === 'DIR_GENERA') {
                $queryValidation->where('statut_demande', 'en_attente_n4');
            }

            $stats['en_attente_validation'] = $queryValidation->count();
        }

        // 4. Statistiques additionnelles pour les RH et l'ADMIN
        if (in_array($user->code_poste, ['RH', 'ADMIN'])) {
            $stats['total_employes'] = Employe::count();
        }

        // On passe le $user optimisé à la vue
        return view('dashboard', compact('stats', 'user'));
    }
}