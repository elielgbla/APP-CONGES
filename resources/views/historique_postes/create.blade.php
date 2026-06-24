@extends('layouts.app') {{-- Assure-toi que ce layout correspond à ton projet (ex: layouts.dashboard ou layouts.app) --}}

@section('content')
<div class="container-fluid p-4">

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <div>
            <h4 class="fw-bold text-uppercase text-success mb-1">Nouvelle Affectation</h4>
            <small class="text-muted">Mutation ou changement de poste / service pour un agent</small>
        </div>
        <div>
            <a href="{{ route('employes.index') }}" class="btn btn-sm btn-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6 class="fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i>Veuillez corriger les erreurs suivantes :</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-xl-8 col-lg-10 mx-auto">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-dark text-white py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="fa-solid fa-id-card-clip me-2"></i>Formulaire d'Affectation Historique</h6>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('historique_postes.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="matricule_employe" class="form-label fw-bold text-muted small">Sélectionner l'Agent <span class="text-danger">*</span></label>
                                <select class="form-select @error('matricule_employe') is-invalid @enderror" id="matricule_employe" name="matricule_employe" required>
                                    <option value="" selected disabled>-- Choisir un employé --</option>
                                    @foreach($employes as $employe)
                                        <option value="{{ $employe->matricule_employe }}" {{ old('matricule_employe') == $employe->matricule_employe ? 'selected' : '' }}>
                                            [{{ $employe->matricule_employe }}] {{ $employe->nom }} {{ $employe->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text text-muted small">L'ancienne affectation active de cet agent sera automatiquement clôturée.</div>
                            </div>
                        </div>

                        <hr class="text-muted opacity-25 mb-4">

                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <label for="code_poste" class="form-label fw-bold text-muted small">Nouveau Poste / Fonction <span class="text-danger">*</span></label>
                                <select class="form-select @error('code_poste') is-invalid @enderror" id="code_poste" name="code_poste" required>
                                    <option value="" selected disabled>-- Choisir le poste --</option>
                                    @foreach($postes as $poste)
                                        <option value="{{ $poste->code_poste }}" {{ old('code_poste') == $poste->code_poste ? 'selected' : '' }}>
                                            {{ $poste->libelle_poste }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="code_service" class="form-label fw-bold text-muted small">Nouveau Service Affecté <span class="text-danger">*</span></label>
                                <select class="form-select @error('code_service') is-invalid @enderror" id="code_service" name="code_service" required>
                                    <option value="" selected disabled>-- Choisir le service --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->code_service }}" {{ old('code_service') == $service->code_service ? 'selected' : '' }}>
                                            {{ $service->libelle_service }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="date_debut" class="form-label fw-bold text-muted small">Date de Prise de Fonction <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_debut') is-invalid @enderror" id="date_debut" name="date_debut" value="{{ old('date_debut', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="p-2 bg-light border rounded small text-secondary w-100" style="font-size: 0.8rem;">
                                    <i class="fa-solid fa-circle-info text-success me-1"></i> La <strong>Date Fin</strong> est laissée vide (<code>NULL</code>) par défaut pour définir cette affectation comme celle actuellement "Active".
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 border-top pt-3 mt-4">
                            <button type="reset" class="btn btn-light btn-sm text-dark px-3">Annuler</button>
                            <button type="submit" class="btn btn-success btn-sm px-4 fw-bold">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Enregistrer la mutation
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection