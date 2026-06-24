@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('plannings.show', $planning->matricule_employe) }}" class="btn btn-sm btn-outline-secondary me-3">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0 text-dark">Modifier l'Emploi du Temps</h4>
            </div>

            @if(session('error'))
                <div class="alert alert-danger border-0 small py-2 mb-3 shadow-sm">{{ session('error') }}</div>
            @endif

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="{{ route('plannings.update', $planning->code_planning) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- AGENT AFFECTÉ -->
                    <div class="mb-3">
                        <label for="matricule_employe" class="form-label small fw-bold text-muted">Agent affecté</label>
                        <select name="matricule_employe" id="matricule_employe" class="form-select form-select-sm" required>
                            @foreach($employes as $emp)
                                <option value="{{ $emp->matricule_employe }}"
                                    {{ (old('matricule_employe', $planning->matricule_employe) == $emp->matricule_employe) ? 'selected' : '' }}>
                                    [{{ $emp->matricule_employe }}] {{ strtoupper($emp->nom) }} {{ $emp->prenom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- DATES -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="date_debut" class="form-label small fw-bold text-muted">Date Début</label>
                            <input type="date" name="date_debut" id="date_debut" class="form-control form-control-sm" value="{{ old('date_debut', $planning->date_debut) }}" required>
                        </div>
                        <div class="col-6">
                            <label for="date_fin" class="form-label small fw-bold text-muted">Date Fin</label>
                            <input type="date" name="date_fin" id="date_fin" class="form-control form-control-sm" value="{{ old('date_fin', $planning->date_fin) }}" required>
                        </div>
                    </div>

                    <!-- HEURES -->
                    <div class="row mb-3">
                        <div class="col-6">
                            <label for="heure_debut" class="form-label small fw-bold text-muted">Heure Début</label>
                            <input type="time" name="heure_debut" id="heure_debut" class="form-control form-control-sm" value="{{ old('heure_debut', substr($planning->heure_debut, 0, 5)) }}" required>
                        </div>
                        <div class="col-6">
                            <label for="heure_fin" class="form-label small fw-bold text-muted">Heure Fin</label>
                            <input type="time" name="heure_fin" id="heure_fin" class="form-control form-control-sm" value="{{ old('heure_fin', substr($planning->heure_fin, 0, 5)) }}" required>
                        </div>
                    </div>

                    <!-- JOURS DE REPOS (Nouveau) -->
                    <div class="row mb-4">
                        <div class="col-6">
                            <label for="jour_repos_1" class="form-label small fw-bold text-muted">Jour Repos 1</label>
                            <select name="jour_repos_1" id="jour_repos_1" class="form-select form-select-sm" required>
                                <option value="">Choisir...</option>
                                @foreach($joursSemaine as $num => $nom)
                                    <option value="{{ $num }}" {{ old('jour_repos_1', $planning->jour_repos_1) == $num ? 'selected' : '' }}>
                                        {{ $nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="jour_repos_2" class="form-label small fw-bold text-muted">Jour Repos 2</label>
                            <select name="jour_repos_2" id="jour_repos_2" class="form-select form-select-sm" required>
                                <option value="">Choisir...</option>
                                @foreach($joursSemaine as $num => $nom)
                                    <option value="{{ $num }}" {{ old('jour_repos_2', $planning->jour_repos_2) == $num ? 'selected' : '' }}
                                        {{ old('jour_repos_1', $planning->jour_repos_1) == $num ? 'disabled' : '' }}>
                                        {{ $nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- BOUTONS D'ACTION -->
                    <div class="text-end border-top pt-3">
                        <a href="{{ route('plannings.show', $planning->matricule_employe) }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">Sauvegarder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const r1 = document.getElementById('jour_repos_1');
    const r2 = document.getElementById('jour_repos_2');

    function updateExclusion() {
        const selected = r1.value;
        Array.from(r2.options).forEach(option => {
            if (option.value !== "") {
                option.disabled = (option.value === selected);
            }
        });
        if (r2.value === selected) {
            r2.value = "";
        }
    }

    r1.addEventListener('change', updateExclusion);
});
</script>
@endsection