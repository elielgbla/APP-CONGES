@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-3">
                <a href="{{ route('residences.show', $employe->matricule_employe) }}" class="text-decoration-none text-muted small">
                    <i class="fas fa-arrow-left me-1"></i> Annuler et retourner à la fiche
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="card-title mb-0 font-weight-bold">
                        <i class="fas fa-edit me-2"></i>Modifier l'Adresse Référence : <span class="badge bg-dark text-white font-monospace ms-1">{{ $residence->code_residence }}</span>
                    </h5>
                </div>
                <div class="card-body p-4">

                    @if(session('error'))
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('residences.update', $residence->code_residence) }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Section d'information fixe de l'employé lié --}}
                        <div class="mb-4 bg-light p-3 rounded border">
                            <span class="text-muted small d-block text-uppercase fw-bold mb-1">Employé concerné</span>
                            <span class="text-dark fw-bold">[{{ $employe->matricule_employe }}] {{ strtoupper($employe->nom) }} {{ $employe->prenom }}</span>
                        </div>

                        {{-- Modification de la Localité (Affichage par Libellé textuel) --}}
                        <div class="mb-4">
                            <label for="code_localite" class="form-label fw-bold text-secondary">Modifier la Ville / Commune <span class="text-danger">*</span></label>
                            <select class="form-select @error('code_localite') is-invalid @enderror" id="code_localite" name="code_localite" required>
                                @foreach($localites as $localite)
                                    <option value="{{ $localite->code_localite }}" {{ (old('code_localite', $residence->code_localite) == $localite->code_localite) ? 'selected' : '' }}>
                                        {{ $localite->libelle_localite ?? $localite->nom_localite }}
                                    </option>
                                @endforeach
                            </select>
                            @error('code_localite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Modification de l'adresse textuelle avec récupération de l'existant --}}
                        <div class="mb-4">
                            <label for="adresse" class="form-label fw-bold text-secondary">Précisions de l'adresse géographique</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" rows="4" placeholder="Quartier, rue, repère de sécurité...">{{ old('adresse', $residence->adresse) }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        {{-- Boutons d'action --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('residences.show', $employe->matricule_employe) }}" class="btn btn-light px-4">Retour sans modifier</a>
                            <button type="submit" class="btn btn-warning px-4 fw-bold text-dark">
                                <i class="fas fa-sync-alt me-2"></i>Mettre à jour l'historique
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection