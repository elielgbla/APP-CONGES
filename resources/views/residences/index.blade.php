@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestion des Résidences</h1>
            <p class="text-muted small mb-0">Liste du personnel et suivi de l'historique de leurs adresses.</p>
        </div>
        <a href="{{ route('residences.create') }}" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus-circle me-2"></i>Attribuer une nouvelle adresse
        </a>
    </div>

    {{-- Affichage des messages flash de succès ou d'erreur --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-light">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users me-2"></i>Effectif du Personnel</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover align-middle" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>Matricule</th>
                            <th>Nom & Prénoms</th>
                            <th>Service / Département</th>
                            <th>Nombre d'adresses</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employes as $employe)
                            <tr>
                                <td class="fw-bold text-secondary">{{ $employe->matricule_employe }}</td>
                                <td>{{ strtoupper($employe->nom) }} {{ ucwords($employe->prenom) }}</td>
                                {{-- Correction ici : Affichage sécurisé du Libellé du Service à la place du code --}}
                                <td>
                                    @if($employe->service)
                                        <span class="badge bg-light text-dark border px-2 py-1">
                                            {{ $employe->service->libelle_service ?? $employe->service->nom_service }}
                                        </span>
                                    @else
                                        <span class="text-muted small">Non assigné</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $employe->localites_count > 0 ? 'bg-info' : 'bg-warning' }} text-dark px-2 py-1.5">
                                        {{ $employe->localites_count }} adresse(s)
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('residences.show', $employe->matricule_employe) }}" class="btn btn-sm btn-outline-primary px-3">
                                        <i class="fas fa-eye me-1"></i> Voir l'historique
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Aucun employé enregistré pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection