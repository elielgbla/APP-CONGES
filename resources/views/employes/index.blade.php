@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom" style="border-color: #e8e5e1 !important;">
        <div>
            <h1 class="h2 fw-bold text-brown mb-1">Gestion du Personnel</h1>
            <p class="text-muted mb-0">Liste des collaborateurs de l'entreprise et gestion des droits d'accès.</p>
        </div>

        <a href="{{ route('employes.create') }}" class="btn text-white shadow-sm px-4" style="background-color: #2e7d32; font-weight: 500;">
            <i class="fa-solid fa-user-plus me-2"></i> Nouvel Employé
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4 bg-white" style="border-radius: 8px;">
        <div class="card-body p-3">
            <form action="{{ route('employes.index') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-12 col-md-6">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light text-muted border-end-0">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text"
                               name="search"
                               class="form-control form-control-sm border-start-0"
                               placeholder="Rechercher par matricule, nom, prénom ou code poste..."
                               value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts de compte</option>
                        <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="inactif" {{ request('statut') === 'inactif' ? 'selected' : '' }}>Inactif</option>
                        <option value="suspendu" {{ request('statut') === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                </div>

                <div class="col-6 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-sm text-white w-100" style="background-color: #5d4037;">
                        Filtrer
                    </button>

                    @if(request()->filled('search') || request()->filled('statut'))
                        <a href="{{ route('employes.index') }}" class="btn btn-sm btn-light border w-100">
                            Réinitialiser
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm bg-white mb-4" style="border-radius: 8px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="text-white" style="background-color: #4a3b32;">
                    <tr>
                        <th class="ps-4" style="width: 70px;">Profil</th>
                        <th>Matricule</th>
                        <th>Identité</th>
                        <th>Poste / Service</th>
                        <th>Solde Congé</th>
                        <th class="text-center">Statut</th>
                        <th class="text-end pe-4" style="width: 150px;">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($employes as $emp)
                        <tr>
                            <td class="ps-4">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-brown fw-bold shadow-sm"
                                     style="width: 38px; height: 38px; border: 2px solid #efece6; font-size: 0.85rem;"
                                     title="{{ $emp->nom }} {{ $emp->prenom }}">
                                    {{ strtoupper(substr($emp->nom, 0, 1)) }}{{ strtoupper(substr($emp->prenom, 0, 1)) }}
                                </div>
                            </td>

                            <td>
                                <strong class="font-monospace text-dark">{{ $emp->matricule_employe }}</strong>
                            </td>

                            <td>
                                <div class="fw-bold text-brown">{{ $emp->nom }}</div>
                                <div class="small text-muted">{{ $emp->prenom }}</div>
                            </td>

                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 small fw-semibold">
                                    {{ $emp->code_poste ?? ($emp->historiquePostes->first()->code_poste ?? 'Non assigné') }}
                                </span>
                                <div class="small text-muted mt-1">
                                    <i class="fa-solid fa-sitemap me-1"></i> {{ $emp->code_service ?? ($emp->historiquePostes->first()->code_service ?? 'N/A') }}
                                </div>
                            </td>

                            <td>
                                <span class="fw-bold text-dark">{{ $emp->total_conge_restant }}</span>
                                <span class="small text-muted">Jours</span>
                            </td>

                            <td class="text-center">
                                @if($emp->statut_compte === 'actif')
                                    <span class="badge bg-success px-2 py-1 rounded-pill small">Actif</span>
                                @elseif($emp->statut_compte === 'inactif')
                                    <span class="badge bg-secondary px-2 py-1 rounded-pill small">Inactif</span>
                                @else
                                    <span class="badge bg-danger px-2 py-1 rounded-pill small">Suspendu</span>
                                @endif
                            </td>

                            <td class="text-end pe-4">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('employes.show', $emp->matricule_employe) }}"
                                       class="btn btn-light"
                                       style="border-color: #dcd7ce; color: #4a3b32;"
                                       title="Fiche professionnelle">
                                        <i class="fa-solid fa-id-card text-primary"></i>
                                    </a>

                                    <a href="{{ route('employes.edit', $emp->matricule_employe) }}"
                                       class="btn btn-light"
                                       style="border-color: #dcd7ce; color: #4a3b32;"
                                       title="Modifier l'employé">
                                        <i class="fa-solid fa-pen-to-square text-warning"></i>
                                    </a>

                                    <form action="{{ route('employes.destroy', $emp->matricule_employe) }}"
                                          method="POST"
                                          class="d-inline m-0 p-0"
                                          onsubmit="return confirm('Êtes-vous certain de vouloir retirer l\'employé ({{ $emp->matricule_employe }}) de l\'effectif ?');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-light h-100"
                                                style="border-color: #dcd7ce; color: #4a3b32; border-top-left-radius: 0; border-bottom-left-radius: 0;"
                                                title="Supprimer"
                                                {{ Auth::user()->matricule_employe === $emp->matricule_employe ? 'disabled' : '' }}>
                                            <i class="fa-solid fa-trash-can text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center p-5 text-muted">
                                <i class="fa-solid fa-users-slash fa-2x mb-3 d-block opacity-50"></i>
                                Aucun employé trouvé correspondant aux critères de recherche.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employes->hasPages())
            <div class="card-footer bg-light p-3 border-top" style="border-color: #efece6 !important;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <span class="small text-muted">
                        Affichage de {{ $employes->firstItem() }} à {{ $employes->lastItem() }} sur {{ $employes->total() }} employés.
                    </span>

                    <div>
                        {{ $employes->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .text-brown {
        color: #4a3b32 !important;
    }

    .page-item.active .page-link {
        background-color: #4a3b32 !important;
        border-color: #4a3b32 !important;
        color: #ffffff !important;
    }

    .page-link {
        color: #4a3b32 !important;
    }
</style>
@endsection
