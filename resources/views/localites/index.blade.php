@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">

    <!-- En-tête de page -->
    <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom" style="border-color: #e8e5e1 !important;">
        <div>
            <h1 class="h2 fw-bold" style="color: #4a3b32;">Gestion des Localités</h1>
            <p class="text-muted mb-0">Référentiel géographique des agences et zones d'habitation.</p>
        </div>
        <div>
            <a href="{{ route('localites.create') }}" class="btn btn-sm text-white px-3" style="background-color: #2e7d32; font-weight: 500; border-radius: 6px;">
                <i class="fa-solid fa-plus me-1"></i> Ajouter une localité
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 8px; background-color: #e8f5e9; color: #1b5e20;">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 8px; background-color: #ffebee; color: #c62828;">
        <i class="fa-solid fa-circle-xmark me-2"></i> {{ session('error') }}
    </div>
    @endif

    <!-- Tableau de suivi -->
    <div class="card border-0 shadow-sm bg-white" style="border-radius: 8px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="text-white font-monospace" style="background-color: #4a3b32;">
                    <tr>
                        <th class="ps-3 py-3" style="width: 20%;">Code Localité</th>
                        <th>Libellé</th>
                        <th class="text-center">Agents Rattachés</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($localites as $localite)
                    <tr class="border-bottom" style="border-color: #f2eee9 !important;">
                        <td class="ps-3 fw-bold text-secondary font-monospace">{{ $localite->code_localite }}</td>
                        <td class="fw-semibold text-dark">{{ $localite->libelle_localite }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ method_exists($localite, 'employesNis') ? $localite->employesNis->count() : ($localite->residences ? $localite->residences->count() : 0) }}</span>
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('localites.edit', $localite->code_localite) }}" class="btn btn-outline-warning" title="Modifier">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <form action="{{ route('localites.destroy', $localite->code_localite) }}" method="POST" onsubmit="return confirm('Supprimer cette localité ?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">Aucune localité enregistrée.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination intégrée proprement au bas de la carte -->
        @if($localites->hasPages())
        <div class="card-footer p-3 d-flex justify-content-center bg-white border-top" style="border-color: #f2eee9 !important;">
            {!! $localites->links() !!}
        </div>
        @endif
    </div>

</div>

<style>
    .page-item.active .page-link {
        background-color: #2e7d32 !important;
        border-color: #2e7d32 !important;
        color: #ffffff !important;
    }
    .page-link {
        color: #4a3b32 !important;
    }
</style>
@endsection