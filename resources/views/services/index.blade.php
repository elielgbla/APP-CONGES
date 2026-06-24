@extends('layouts.app')

@section('content')
<div class="container my-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Services & Directions</h1>
            <p class="text-muted small mb-0">Gestion de la structure organisationnelle et des départements de la SOGETHO-SA.</p>
        </div>
        <a href="{{ route('services.create') }}" class="btn btn-sm btn-success px-3">
            <i class="fa-solid fa-plus me-1"></i> Ajouter un service
        </a>
    </div>

    <div class="row mb-3">
        <div class="col-md-5">
            <form action="{{ route('services.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Rechercher par code ou libellé..." value="{{ $search }}">
                    @if(!empty($search))
                        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary d-flex align-items-center bg-white border-start-0 text-muted px-2" title="Effacer la recherche">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-sm btn-dark px-3">Filtrer</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm small py-2 mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm small py-2 mb-3">
            <i class="fa-solid fa-circle-xmark me-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0 small">
                <thead class="table-light text-secondary">
                    <tr>
                        <th style="width: 25%;">Code Unique</th>
                        <th style="text-align: left; width: 45%;">Libellé du Service / Direction</th>
                        <th style="width: 15%;">Effectif Personnel</th>
                        <th style="width: 15%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($services as $item)
                        <tr>
                            <td class="fw-bold text-secondary text-uppercase">
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $item->code_service }}</span>
                            </td>
                            <td class="text-start fw-bold text-dark">
                                {{ $item->libelle_service }}
                            </td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success fw-bold px-3 py-1 rounded-pill">
                                    {{ $item->employes_count }} agent(s)
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('services.show', $item->code_service) }}" class="btn btn-sm btn-outline-info py-0 px-2" title="Consulter la fiche">
                                        <i class="fa-solid fa-eye"></i> Voir
                                    </a>
                                    <a href="{{ route('services.edit', $item->code_service) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <form action="{{ route('services.destroy', $item->code_service) }}" method="POST" onsubmit="return confirm('Voulez-vous dissoudre ce service ? Cette action est irréversible.');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Supprimer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted py-4">Aucun département ne correspond à vos critères de recherche.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection