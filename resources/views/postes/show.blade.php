@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <a href="{{ route('postes.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="fa-solid fa-arrow-left"></i> Retour</a>
            <h2 class="h4 fw-bold text-dark mb-0">Fiche Métier : {{ $poste->code_poste }}</h2>
        </div>
        <div class="d-flex gap-2">
            <button type="button" onclick="ouvrirFenetreImpression('{{ route('postes.imprimerFiche', $poste->code_poste) }}')" class="btn btn-sm btn-dark px-3">
                <i class="fa-solid fa-print me-1"></i> Imprimer
            </button>
            <a href="{{ route('postes.edit', $poste->code_poste) }}" class="btn btn-sm btn-primary px-3">Modifier</a>
        </div>
    </div>

    <div class="card shadow-sm border-0 p-3 mb-4 bg-light">
        <span class="text-muted small fw-bold text-uppercase">Intitulé officiel de l'emploi :</span>
        <h4 class="fw-bold text-success mb-0 mt-1">{{ $poste->libelle_poste }}</h4>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 pt-3">
            <h5 class="fw-bold text-secondary mb-0" style="font-size: 1rem;">Titulaires du poste</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0 small mt-2">
                <thead class="table-light text-muted">
                    <tr>
                        <th>Matricule</th>
                        <th style="text-align: left;">Nom & Prénoms</th>
                        <th style="text-align: left;">Service / Département Affilié</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($poste->employes as $agent)
                        <tr>
                            <td class="fw-bold text-danger">{{ $agent->matricule_employe }}</td>
                            <td class="text-start fw-bold text-dark text-uppercase">{{ $agent->nom }} {{ $agent->prenom }}</td>
                            <td class="text-start">{{ $agent->service->libelle_service ?? 'Non affecté' }}</td>
                            <td>
                                <a href="{{ route('employes.show', $agent->matricule_employe) }}" class="btn btn-sm btn-light border py-0 px-2">
                                    <i class="fa-solid fa-user me-1 text-muted"></i> Profil
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted py-4">Aucun agent n'est actuellement enregistré sous cette qualification.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-2 text-end small border-top text-muted">
            Effectif total pour cette qualification : <strong class="text-primary">{{ $poste->employes->count() }} agent(s)</strong>
        </div>
    </div>
</div>
@endsection