<?php

namespace App\Http\Controllers;

use App\Models\Employe;
use App\Models\Localite;
use App\Models\Residence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;                  // <-- AJOUTÉ
use Illuminate\Routing\Controllers\HasMiddleware;   // <-- AJOUTÉ
use Illuminate\Routing\Controllers\Middleware;      // <-- AJOUTÉ

class ResidenceController extends Controller implements HasMiddleware // <-- AJOUTÉ
{
    /**
     * Applique les restrictions d'accès de manière moderne.
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
     * INDEX : Liste uniquement les employés.
     */
    public function index()
    {
       $employes = Employe::with(['service'])->withCount('localites')->orderBy('nom', 'asc')->get();

        return view('residences.index', compact('employes'));
    }

    /**
     * CREATE : Formulaire d'attribution d'une nouvelle adresse.
     */
    public function create()
    {
        $employes = Employe::orderBy('nom', 'asc')->get();
        $localites = Localite::orderBy('libelle_localite', 'asc')->get();

        return view('residences.create', compact('employes', 'localites'));
    }

    /**
     * STORE : Enregistrement de la nouvelle résidence.
     */
    public function store(Request $request)
    {
        // Correction des règles pour cibler tes tables réelles (employe et localite au singulier)
        $request->validate([
            'matricule_employe' => 'required|exists:employe,matricule_employe',
            'code_localite'     => 'required|exists:localite,code_localite',
            'adresse'           => 'nullable|string|max:255',
        ]);

        try {
            Residence::query()->create([
                'matricule_employe' => $request->matricule_employe,
                'code_localite'     => $request->code_localite,
                'adresse'           => $request->adresse,
            ]);

            return redirect()->route('residences.show', $request->matricule_employe)
                             ->with('success', 'Nouvelle adresse ajoutée avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur Store Residence: " . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de l\'enregistrement.');
        }
    }

    /**
     * SHOW : La Fiche Personnelle de l'employé avec tout son historique.
     */
    public function show(string $matricule)
    {
        // Chargement sécurisé de la relation via le pivot
        $employe = Employe::with(['localites' => function ($query) {
            $query->orderBy('residence.created_at', 'desc');
        }])->where('matricule_employe', $matricule)->firstOrFail();

        return view('residences.show', compact('employe'));
    }

    /**
     * EDIT : Formulaire de modification d'une ligne d'historique spécifique.
     */
    public function edit(string $code_residence)
    {
        $residence = Residence::query()->where('code_residence', $code_residence)->firstOrFail();
        $localites = Localite::orderBy('libelle_localite', 'asc')->get();
        $employe = Employe::where('matricule_employe', $residence->matricule_employe)->firstOrFail();

        return view('residences.edit', compact('residence', 'localites', 'employe'));
    }

    /**
     * UPDATE : Mise à jour de la ligne d'adresse spécifique.
     */
    public function update(Request $request, string $code_residence)
    {
        $request->validate([
            'code_localite' => 'required|exists:localite,code_localite',
            'adresse'       => 'nullable|string|max:255',
        ]);

        try {
            $residence = Residence::query()->where('code_residence', $code_residence)->firstOrFail();
            
            $residence->update([
                'code_localite' => $request->code_localite,
                'adresse'       => $request->adresse,
            ]);

            return redirect()->route('residences.show', $residence->matricule_employe)
                             ->with('success', 'Adresse mise à jour dans l\'historique.');
        } catch (\Exception $e) {
            Log::error("Erreur Update Residence: " . $e->getMessage());
            return back()->with('error', 'Impossible de modifier cet enregistrement.');
        }
    }

    /**
     * DESTROY : Suppression définitive d'une adresse de l'historique.
     */
    public function destroy(string $code_residence)
    {
        try {
            $residence = Residence::query()->where('code_residence', $code_residence)->firstOrFail();
            $matricule = $residence->matricule_employe;
            
            $residence->delete();

            return redirect()->route('residences.show', $matricule)
                             ->with('success', 'Adresse retirée de l\'historique avec succès.');
        } catch (\Exception $e) {
            Log::error("Erreur Destroy Residence: " . $e->getMessage());
            return back()->with('error', 'Erreur lors de la suppression de l\'adresse.');
        }
    }
}