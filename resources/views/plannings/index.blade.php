@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Emploi du Temps du Personnel</h1>
            <p class="text-muted small mb-0">Liste des agents disposant d'un planning actif à la SOGETHO-SA.</p>
        </div>
        <a href="{{ route('plannings.create') }}" class="btn btn-sm btn-success px-3">
            <i class="fa-solid fa-calendar-plus me-1"></i> Fixer un planning
        </a>
    </div>

    <!-- Barre de filtrage et recherche -->
    <div class="row mb-4">
        <div class="col-md-5">
            <form action="{{ route('plannings.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Rechercher un agent (Nom, matricule...)..." value="{{ $search }}">
                </div>
                <button type="submit" class="btn btn-sm btn-dark px-3">Filtrer</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm small py-2 mb-3">{{ session('success') }}</div>
    @endif

    <!-- TABLEAU SIMPLIFIÉ DES PERSONNES PROGRAMMÉES -->
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0 small">
                <thead class="table-light text-muted" style="font-size: 0.75rem;">
                    <tr>
                        <th style="width: 20%;">Matricule</th>
                        <th class="text-start" style="width: 45%;">Nom & Prénoms</th>
                        <th style="width: 15%;">Programmes Actifs</th>
                        <th style="width: 20%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employes as $emp)
                        <tr>
                            <td>
                                <span class="badge bg-danger px-2 py-1" style="font-size: 0.75rem;">{{ $emp->matricule_employe }}</span>
                            </td>
                            <td class="text-start">
                                <strong class="text-dark text-uppercase">{{ $emp->nom }}</strong> {{ $emp->prenom }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border rounded-pill px-3 py-1 fw-semibold">
                                    {{ $emp->planning_count }} créneau(x)
                                </span>
                            </td>
                            <td>
                                <!-- Action unique : Redirection vers le Show pro de l'agent -->
                                <a href="{{ route('plannings.show', $emp->matricule_employe) }}" class="btn btn-sm btn-primary px-3 py-1 rounded-pill" style="font-size: 0.75rem; letter-spacing: 0.3px;">
                                    <i class="fa-solid fa-eye me-1"></i> Voir le planning
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted py-4">
                                <i class="fa-solid fa-calendar-xmark d-block fs-4 mb-2 text-secondary"></i>
                                Aucun agent ne possède de programme actuellement.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection