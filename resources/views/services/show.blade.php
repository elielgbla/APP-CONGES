@extends('layouts.app')

@section('content')
<div class="container my-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
            <h2 class="h4 fw-bold text-dark mb-0">Fiche Technique : Service {{ $service->code_service }}</h2>
        </div>
        <div class="d-flex gap-2">
            <button type="button" onclick="ouvrirFenetreImpression('{{ route('services.imprimerFiche', $service->code_service) }}')" class="btn btn-sm btn-dark px-3">
                <i class="fa-solid fa-print me-1"></i> Imprimer la liste
            </button>
            <a href="{{ route('services.edit', $service->code_service) }}" class="btn btn-sm btn-primary px-3">
                <i class="fa-solid fa-pen-to-square me-1"></i> Modifier
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4" style="border-left: 4px solid #1e5631 !important;">
        <div class="card-body p-3 bg-light bg-opacity-50">
            <div class="row align-items-center">
                <div class="col-md-1 text-center d-none d-md-block">
                    <i class="fa-solid fa-user-tie fa-2x text-success"></i>
                </div>
                <div class="col-md-11">
                    <span class="text-muted small uppercase fw-bold d-block" style="font-size: 0.75rem; letter-spacing: 0.5px;">Superviseur / Responsable Hiérarchique N1 :</span>
                    @if($chefService)
                        <h5 class="fw-bold text-dark mb-0 text-uppercase">{{ $chefService->nom }} {{ $chefService->prenom }}</h5>
                        <small class="text-secondary">Matricule : <strong class="text-danger">{{ $chefService->matricule_employe }}</strong> — Poste : {{ $chefService->poste->libelle_poste ?? 'Responsable' }}</small>
                    @else
                        <h5 class="fw-bold text-muted mb-0 italic" style="font-size: 0.95rem;">Aucun gestionnaire hiérarchique actuellement assigné à ce service.</h5>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 pt-3 pb-0">
            <h5 class="fw-bold text-secondary mb-0" style="font-size: 1rem;">Membres du Personnel</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0 small mt-2">
                <thead class="table-light text-muted">
                    <tr>
                        <th>Matricule</th>
                        <th style="text-align: left;">Nom & Prénoms</th>
                        <th style="text-align: left;">Poste Actuel</th>
                        <th>Téléphone</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($service->employes as $agent)
                        <tr>
                            <td class="fw-bold text-danger">{{ $agent->matricule_employe }}</td>
                            <td class="text-start fw-bold text-dark text-uppercase">
                                {{ $agent->nom }} {{ $agent->prenom }}
                            </td>
                            <td class="text-start">
                                {{ $agent->poste->libelle_poste ?? 'Technique' }}
                            </td>
                            <td>{{ $agent->telephone ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('employes.show', $agent->matricule_employe) }}" class="btn btn-sm btn-light border py-0 px-2" title="Voir profil agent">
                                    <i class="fa-solid fa-user me-1 text-muted"></i> Profil
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-muted py-4">Aucun employé n'est affecté à cette direction pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="card-footer bg-light border-0 py-2 text-end small">
            Total des effectifs enregistrés : <strong class="text-success" style="font-size: 0.95rem;">{{ $service->employes->count() }} personne(s)</strong>
        </div>
    </div>
</div>
@endsection