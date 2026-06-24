@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('residences.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
            <h1 class="h3 mb-0 text-gray-800">Historique des Résidences</h1>
            <p class="text-muted small mb-0">Fiche de suivi des adresses de l'agent.</p>
        </div>
    </div>

    {{-- Alertes de notifications --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        {{-- En-tête / Profil de l'agent --}}
        <div class="col-md-4 mb-4">
            <div class="card shadow border-0 text-center p-4">
                <div class="mb-3">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                        <i class="fas fa-user-tie fa-2x"></i>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-1">{{ strtoupper($employe->nom) }} {{ $employe->prenom }}</h5>
                <p class="text-muted small mb-3">Matricule : <span class="badge bg-secondary">{{ $employe->matricule_employe }}</span></p>
                <hr>
                <div class="text-start small">
                    <p class="mb-2"><strong><i class="fas fa-building me-2 text-muted"></i>Service :</strong> {{ $employe->service->libelle_service ?? $employe->service->nom_service ?? 'Non assigné' }}</p>
                    <p class="mb-0"><strong><i class="fas fa-briefcase me-2 text-muted"></i>Fonction :</strong> {{ $employe->poste->libelle_poste ?? 'Non assignée' }}</p>
                </div>
            </div>
        </div>

        {{-- Tableau historique des adresses --}}
        <div class="col-md-8 mb-4">
            <div class="card shadow border-0">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history me-2"></i>Parcours Résidentiel</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle small">
                            <thead class="table-dark">
                                <tr>
                                    <th>Zone / Localité</th>
                                    <th>Adresse Précise</th>
                                    <th>Date d'enregistrement</th>
                                    <th class="text-center" style="width: 120px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employe->localites as $localite)
                                    <tr>
                                        <td class="fw-bold">{{ $localite->libelle_localite ?? $localite->nom_localite }}</td>
                                        <td>{{ $localite->pivot->adresse ?? 'Aucun détail fourni' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($localite->pivot->created_at)->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                {{-- Modifier accessible à tous les utilisateurs autorisés au contrôleur --}}
                                                <a href="{{ route('residences.edit', $localite->pivot->code_residence) }}" class="btn btn-sm btn-outline-warning" title="Modifier cette adresse">
                                                    <i class="fas fa-edit"></i>
                                                </a>

                                                {{-- CORRECTION ICI : Remplacement de hasRole par la vérification de la colonne code_poste --}}
                                                @if(auth()->check() && in_array(auth()->user()->code_poste, ['ADMIN', 'COMMANDEMENT']))
                                                    <form action="{{ route('residences.destroy', $localite->pivot->code_residence) }}" method="POST" onsubmit="return confirm('Confirmez-vous la suppression définitive de cette adresse de l\'historique ?');" style="display:inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Retirer de l'historique">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="fas fa-info-circle me-1"></i> Aucune adresse historique enregistrée pour cet agent.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection