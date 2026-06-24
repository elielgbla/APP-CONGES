@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom" style="border-color: #e8e5e1 !important;">
        <div>
            <a href="{{ route('demandes.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="color: #4a3b32; border-color: #dcd7ce;">
                <i class="fa-solid fa-arrow-left me-1"></i> Annuler et retourner
            </a>
            <h1 class="h2 fw-bold text-brown">Formuler une demande de congé</h1>
            <p class="text-muted mb-0">Sélectionnez l'agent et soumettez ses dates. La durée sera calculée dynamiquement par le système.</p>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 8px;">
        <ul class="mb-0 small fw-bold">
            @foreach($errors->all() as $error)
                <li><i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('demandes.store') }}" method="POST">
        @csrf

        <div class="row g-4">
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm bg-white" style="border-radius: 8px;">
                    <div class="card-header text-white py-3" style="background-color: #4a3b32;">
                        <h5 class="card-title mb-0 small text-uppercase fw-bold"><i class="fa-solid fa-calendar-plus me-2"></i> Informations & Période d'absence</h5>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Sélectionner l'Employé / l'Agent <span class="text-danger">*</span></label>
                                <select name="matricule_employe" id="matricule_employe" class="form-select form-select-sm" required>
                                    <option value="" disabled selected>Choisir un matricule d'employé...</option>
                                    @foreach($employes as $emp)
                                        <option value="{{ $emp->matricule_employe }}" 
                                                data-nom="{{ $emp->nom }}" 
                                                data-prenom="{{ $emp->prenom }}" 
                                                data-solde="{{ $emp->total_conge_restant }}"
                                                {{ old('matricule_employe') == $emp->matricule_employe ? 'selected' : '' }}>
                                            {{ $emp->matricule_employe }} - {{ $emp->nom }} {{ $emp->prenom }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Nature / Type de Permission <span class="text-danger">*</span></label>
                                <select name="code_type_permission" class="form-select form-select-sm" required>
                                    <option value="" disabled selected>Choisir le motif d'absence...</option>
                                    @foreach($types_conge as $type)
                                        <option value="{{ $type->code_type_permission }}" {{ old('code_type_permission') == $type->code_type_permission ? 'selected' : '' }}>
                                            {{ $type->libelle_type_permission }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Date de Début Prévu <span class="text-danger">*</span></label>
                                <input type="date" name="date_debut_prevu" class="form-control form-control-sm" value="{{ old('date_debut_prevu') }}" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted text-uppercase">Date de Fin Prévu <span class="text-danger">*</span></label>
                                <input type="date" name="date_fin_prevu" class="form-control form-control-sm" value="{{ old('date_fin_prevu') }}" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted text-uppercase">Motif ou justification explicite</label>
                                <textarea name="motif" class="form-control" rows="4" placeholder="Saisir la justification ou les détails de la permission d'absence...">{{ old('motif') }}</textarea>
                            </div>
                        </div>

                        <button type="submit" class="btn text-white px-4 py-2 fw-bold" style="background-color: #2e7d32;">
                            <i class="fa-solid fa-paper-plane me-2"></i> Enregistrer et soumettre
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm bg-white" style="border-radius: 8px;">
                    <div class="card-header text-dark py-3 border-bottom bg-light" style="border-radius: 8px 8px 0 0;">
                        <h5 class="card-title mb-0 small text-uppercase fw-bold text-muted"><i class="fa-solid fa-user-check me-2"></i> Vérification Identité</h5>
                    </div>
                    <div class="card-body p-4" id="zone_info_employe">
                        <div class="text-center text-muted py-3">
                            <i class="fa-solid fa-user-secret fa-2x mb-2 text-opacity-25"></i>
                            <p class="mb-0 small">Aucun agent sélectionné pour le moment.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectEmploye = document.getElementById('matricule_employe');
    const zoneInfo = document.getElementById('zone_info_employe');

    function mettreAJourZone() {
        const selectedOption = selectEmploye.options[selectEmploye.selectedIndex];

        if (selectedOption && selectedOption.value !== "") {
            const nom = selectedOption.getAttribute('data-nom');
            const prenom = selectedOption.getAttribute('data-prenom');
            const solde = selectedOption.getAttribute('data-solde');

            zoneInfo.innerHTML = `
                <div class="mb-3">
                    <label class="small text-muted text-uppercase d-block fw-semibold">Nom & Prénoms</label>
                    <div class="fs-5 fw-bold text-dark">${nom} ${prenom}</div>
                </div>
                <div class="mb-1">
                    <label class="small text-muted text-uppercase d-block fw-semibold">Droit aux absences</label>
                    <div class="alert bg-success bg-opacity-10 text-success border-0 py-2 px-3 mt-1 fw-bold d-inline-flex align-items-center" style="border-radius:6px;">
                        <i class="fa-solid fa-wallet me-2"></i> Solde restant : ${solde} jours
                    </div>
                </div>
            `;
        } else {
            zoneInfo.innerHTML = `
                <div class="text-center text-muted py-3">
                    <i class="fa-solid fa-user-secret fa-2x mb-2 text-opacity-25"></i>
                    <p class="mb-0 small">Aucun agent sélectionné pour le moment.</p>
                </div>
            `;
        }
    }

    // Déclencher au changement et au rechargement de page si une ancienne valeur (old) existe
    selectEmploye.addEventListener('change', mettreAJourZone);
    mettreAJourZone();
});
</script>
@endsection