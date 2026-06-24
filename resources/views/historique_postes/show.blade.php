@extends('layouts.app')

@section('content')
<div class="container-fluid my-5 px-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('historique_postes.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold mb-1 text-dark">Parcours & Mutations de l'Agent</h4>
            <p class="text-muted small mb-0">Registre complet des postes occupés par l'employé au sein de l'organisation.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm small py-2" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0 bg-white p-4 mb-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="text-muted small text-uppercase fw-bold tracking-wider">Fiche Agent</span>
                <h4 class="fw-bold text-success mb-1 mt-1">
                    {{ strtoupper($employe->nom) }} {{ $employe->prenom }}
                </h4>
                <p class="mb-0 text-secondary font-monospace small">Matricule unique : {{ $employe->matricule_employe }}</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <span class="text-muted small d-block">Total des mouvements</span>
                <span class="badge bg-dark px-3 py-2 fs-6 rounded-pill fw-bold">{{ $mutations->count() }} Mutation(s)</span>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 bg-white">
        <div class="card-header bg-light py-3 border-0">
            <h6 class="mb-0 fw-bold text-secondary"><i class="fa-solid fa-timeline me-2"></i>Lignes d'affectations successives</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 0.875rem;">
                <thead class="table-light text-uppercase tracking-wider small text-muted">
                    <tr>
                        <th class="ps-4">Réf Code</th>
                        <th>Poste / Fonction</th>
                        <th>Service / Département</th>
                        <th>Date Début</th>
                        <th>Date Fin</th>
                        <th>Statut</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mutations as $mutation)
                        <tr>
                            <td class="ps-4 fw-bold text-secondary">#{{ $mutation->code_historique }}</td>
                            <td>
                                <span class="badge bg-light text-dark border px-2 py-1 fw-semibold">
                                    {{ $mutation->poste->libelle_poste ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="fw-semibold text-dark">{{ $mutation->service->libelle_service ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::parse($mutation->date_debut)->format('d/m/Y') }}</td>
                            <td>
                                {{ $mutation->date_fin ? \Carbon\Carbon::parse($mutation->date_fin)->format('d/m/Y') : '-' }}
                            </td>
                            <td>
                                @if(is_null($mutation->date_fin))
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">Poste Actif</span>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill small">Historique</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('historique_postes.edit', $mutation->code_historique) }}" class="btn btn-light border text-warning" title="Modifier cette ligne"><i class="fa-solid fa-pen-to-square"></i></a>
                                    
                                    <form action="{{ route('historique_postes.destroy', $mutation->code_historique) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne de mutation ?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light border text-danger" title="Supprimer"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <p class="mb-0">Cet employé n'a aucun historique d'affectation enregistré.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection