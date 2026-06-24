@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-3">
                <a href="{{ route('residences.index') }}" class="text-decoration-none text-muted small">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>

            <div class="card shadow">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0"><i class="fas fa-map-marker-alt me-2"></i>Attribuer une Adresse</h5>
                </div>
                <div class="card-body p-4">
                    
                    @if(session('error'))
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('residences.store') }}" method="POST">
                        @csrf

                        {{-- Sélection de l'employé --}}
                        <div class="mb-4">
                            <label for="matricule_employe" class="form-label fw-bold text-secondary">Sélectionner l'Employé <span class="text-danger">*</span></label>
                            <select class="form-select @error('matricule_employe') is-invalid @enderror" id="matricule_employe" name="matricule_employe" required>
                                <option value="" selected disabled>-- Choisir un employé --</option>
                                @foreach($employes as $employe)
                                    <option value="{{ $employe->matricule_employe }}" {{ old('matricule_employe') == $employe->matricule_employe ? 'selected' : '' }}>
                                        [{{ $employe->matricule_employe }}] {{ strtoupper($employe->nom) }} {{ $employe->prenom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('matricule_employe')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Sélection de la Localité (Affichage par Libellé corrigé) --}}
                        <div class="mb-4">
                            <label for="code_localite" class="form-label fw-bold text-secondary">Ville / Commune (Localité) <span class="text-danger">*</span></label>
                            <select class="form-select @error('code_localite') is-invalid @enderror" id="code_localite" name="code_localite" required>
                                <option value="" selected disabled>-- Choisir la zone géographique --</option>
                                @foreach($localites as $localite)
                                    <option value="{{ $localite->code_localite }}" {{ old('code_localite') == $localite->code_localite ? 'selected' : '' }}>
                                        {{ $localite->libelle_localite ?? $localite->nom_localite }}
                                    </option>
                                @endforeach
                            </select>
                            @error('code_localite')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Adresse textuelle précise --}}
                        <div class="mb-4">
                            <label for="adresse" class="form-label fw-bold text-secondary">Détails de l'adresse (Quartier, Rue, Porte, etc.)</label>
                            <textarea class="form-control @error('adresse') is-invalid @enderror" id="adresse" name="adresse" rows="4" placeholder="Ex: Cocody Angré, 8ème Tranche, non loin de la pharmacie, Immeuble Horizon Appt B4...">{{ old('adresse') }}</textarea>
                            @error('adresse')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-muted small mb-4">
                            <i class="fas fa-info-circle text-info"></i> Le code de l'adresse sera généré automatiquement par le système selon la nomenclature interne.
                        </div>

                        <hr>

                        {{-- Actions de soumission --}}
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('residences.index') }}" class="btn btn-light px-4">Annuler</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Enregistrer l'adresse
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection