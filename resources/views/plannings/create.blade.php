@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('plannings.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0 text-dark">Planifier un Créneau Horaire</h4>
            </div>

            @if(session('error'))
                <div class="alert alert-danger border-0 small py-2 mb-3 shadow-sm">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="{{ route('plannings.store') }}" method="POST">
                    @csrf

                    <!-- SÉLECTION AGENT (AVEC PRÉ-SÉLECTION CONTEXTUELLE) -->
                    <div class="mb-3">
                        <label for="matricule_employe" class="form-label small fw-bold text-muted">Affecter à l'agent</label>
                        <select name="matricule_employe" id="matricule_employe" class="form-select form-select-sm" required>
                            <option value="" disabled {{ !old('matricule_employe') && !$matriculePreselectionne ? 'selected' : '' }}>
                                -- Choisir l'employé --
                            </option>
                            @foreach($employes as $emp)
                                <option value="{{ $emp->matricule_employe }}"
                                    {{ (old('matricule_employe') ?? $matriculePreselectionne) == $emp->matricule_employe ? 'selected' : '' }}>
                                    [{{ $emp->matricule_employe }}] {{ strtoupper($emp->nom) }} {{ $emp->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- DATES -->
                    <div class="row mb-3">
                        <div class="col-12">
                        <label for="date_debut" class="form-label small fw-bold text-muted">Date de Prise d'Effet (Début)</label>
                           <input type="date" name="date_debut" id="date_debut" class="form-control form-control-sm" value="{{ old('date_debut', date('Y-m-d')) }}" required>
                        </div>
                    </div>

                    <!-- HEURES -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="heure_debut" class="form-label small fw-bold text-muted">Heure Début Prise en Charge</label>
                            <input type="time" name="heure_debut" id="heure_debut" class="form-control form-control-sm" value="{{ old('heure_debut', '08:00') }}" required>
                        </div>
                        <div class="col-6">
                            <label for="heure_fin" class="form-label small fw-bold text-muted">Heure Fin de Service</label>
                            <input type="time" name="heure_fin" id="heure_fin" class="form-control form-control-sm" value="{{ old('heure_fin', '17:00') }}" required>
                        </div>
                    </div>

                    <!-- CONFIGURATION DES JOURS DE REPOS (Nouveaux champs alignés avec le calendrier) -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <label for="jour_repos_1" class="form-label small fw-bold text-muted">Premier Jour de Repos</label>
                            <select name="jour_repos_1" id="jour_repos_1" class="form-select form-select-sm" required>
                                <option value="" disabled {{ !old('jour_repos_1') ? 'selected' : '' }}>-- Choisir --</option>
                                <option value="1" {{ old('jour_repos_1') == '1' ? 'selected' : '' }}>Lundi</option>
                                <option value="2" {{ old('jour_repos_1') == '2' ? 'selected' : '' }}>Mardi</option>
                                <option value="3" {{ old('jour_repos_1') == '3' ? 'selected' : '' }}>Mercredi</option>
                                <option value="4" {{ old('jour_repos_1') == '4' ? 'selected' : '' }}>Jeudi</option>
                                <option value="5" {{ old('jour_repos_1') == '5' ? 'selected' : '' }}>Vendredi</option>
                                <option value="6" {{ old('jour_repos_1') == '6' ? 'selected' : '' }}>Samedi</option>
                                <option value="7" {{ old('jour_repos_1') == '7' ? 'selected' : '' }}>Dimanche</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="jour_repos_2" class="form-label small fw-bold text-muted">Second Jour de Repos (Optionnel)</label>
                            <select name="jour_repos_2" id="jour_repos_2" class="form-select form-select-sm">
                                <option value="" {{ !old('jour_repos_2') ? 'selected' : '' }}>Aucun</option>
                                <option value="1" {{ old('jour_repos_2') == '1' ? 'selected' : '' }}>Lundi</option>
                                <option value="2" {{ old('jour_repos_2') == '2' ? 'selected' : '' }}>Mardi</option>
                                <option value="3" {{ old('jour_repos_2') == '3' ? 'selected' : '' }}>Mercredi</option>
                                <option value="4" {{ old('jour_repos_2') == '4' ? 'selected' : '' }}>Jeudi</option>
                                <option value="5" {{ old('jour_repos_2') == '5' ? 'selected' : '' }}>Vendredi</option>
                                <option value="6" {{ old('jour_repos_2') == '6' ? 'selected' : '' }}>Samedi</option>
                                <option value="7" {{ old('jour_repos_2') == '7' ? 'selected' : '' }}>Dimanche</option>
                            </select>
                        </div>
                    </div>

                    <!-- BOUTONS D'ACTION -->
                    <div class="text-end border-top pt-3">
                        <a href="{{ route('plannings.index') }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">Valider le créneau</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection