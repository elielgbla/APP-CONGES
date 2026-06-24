@extends('layouts.app')

@section('content')
<div class="container-fluid my-5 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Historique des Affectations & Mutations</h3>
            <p class="text-muted small mb-0">Suivi des mouvements, changements de postes et services des agents.</p>
        </div>
        <a href="{{ route('historique_postes.create') }}" class="btn btn-sm btn-success fw-bold px-3 shadow-sm">
            <i class="fa-solid fa-user-plus me-2"></i> Opérer une Mutation
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm small py-2" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 mb-4 bg-white p-3">
        <form action="{{ route('historique_postes.index') }}" method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted border-end-0"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-start-0 ps-0" placeholder="Rechercher par Matricule ou Nom..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-secondary fw-bold px-3">Filtrer</button>
                @if(!empty($search))
                    <a href="{{ route('historique_postes.index') }}" class="btn btn-sm btn-link text-muted small">Réinitialiser</a>
                @endif
            </div>
        </form>
    </div>

    <div class="card shadow-sm border-0 bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 0.875rem;">
                <thead class="table-light text-uppercase tracking-wider small text-muted">
                    <tr>
                        <th class="ps-4">Agent</th>
                        <th>Poste Actuel</th>
                        <th>Service Actuel</th>
                        <th>Date d'Installation</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employes as $emp)
                          @php
            // On extrait l'affectation active chargée impérativement via la relation
            $affectationActuelle = $emp->historiquePostes->first();
        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ strtoupper($emp->nom) }} {{ $emp->prenom }}</div>
                                <div class="text-muted small" style="font-size: 0.75rem;">Matricule: {{ $emp->matricule_employe }}</div>
                            </td>
                            <td>
                                @if($affectationActuelle && $affectationActuelle->poste)
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ $affectationActuelle->poste->libelle_poste }}</span>
                                @else
                                    <span class="text-muted small">Aucun poste assigné</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-dark fw-semibold">
                                    {{ $affectationActuelle->service->libelle_service ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                {{ $affectationActuelle ? \Carbon\Carbon::parse($affectationActuelle->date_debut)->format('d/m/Y') : '-' }}
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('historique_postes.show', $emp->matricule_employe) }}" class="btn btn-sm btn-light border text-primary fw-bold" title="Voir l'historique complet">
                                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Voir l'historique
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open fa-2x mb-3 text-secondary"></i>
                                <p class="mb-0">Aucun agent trouvé ou aucune affectation répertoriée.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($employes->hasPages())
            <div class="card-footer bg-white border-0 pt-3 pb-2 px-4">
                {{ $employes->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
