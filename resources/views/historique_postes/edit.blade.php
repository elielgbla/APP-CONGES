@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('historique_postes.show', $historique->matricule_employe) }}" class="btn btn-sm btn-outline-secondary me-3" title="Retour à l'historique de l'agent">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0 text-dark">Modifier la Ligne d'Affectation #{{ $historique->code_historique }}</h4>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                {{-- Bloc d'information Agent --}}
                <div class="alert alert-warning border-0 small py-2 mb-4">
                    <i class="fa-solid fa-user me-2"></i> Agent : 
                    <strong>
                        [{{ $historique->matricule_employe }}] 
                        @if($historique->employe)
                            {{ strtoupper($historique->employe->nom) }} {{ $historique->employe->prenom }}
                        @else
                            <span class="text-danger">Agent introuvable ou supprimé</span>
                        @endif
                    </strong>
                </div>

                <form action="{{ route('historique_postes.update', $historique->code_historique) }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Sélection du Poste / Fonction pré-remplie --}}
                    <div class="mb-3">
                        <label for="code_poste" class="form-label small fw-bold text-muted">Poste / Fonction</label>
                        <select name="code_poste" id="code_poste" class="form-select form-select-sm" required>
                            <option value="" disabled>-- Choisir la fonction --</option>
                            @foreach($postes as $p)
                                <option value="{{ $p->code_poste }}" {{ $historique->code_poste === $p->code_poste ? 'selected' : '' }}>
                                    {{ $p->libelle_poste }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Sélection du Service / Affectation pré-remplie --}}
                    <div class="mb-3">
                        <label for="code_service" class="form-label small fw-bold text-muted">Service / Affectation</label>
                        <select name="code_service" id="code_service" class="form-select form-select-sm" required>
                            <option value="" disabled>-- Choisir la direction --</option>
                            @foreach($services as $s)
                                <option value="{{ $s->code_service }}" {{ $historique->code_service === $s->code_service ? 'selected' : '' }}>
                                    {{ $s->libelle_service }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Dates d'affectation récupérées et formatées au standard HTML (Y-m-d) --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date_debut" class="form-label small fw-bold text-muted">Date de Début</label>
                            <input type="date" name="date_debut" id="date_debut" class="form-control form-control-sm" 
                                   value="{{ $historique->date_debut ? \Carbon\Carbon::parse($historique->date_debut)->format('Y-m-d') : '' }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="date_fin" class="form-label small fw-bold text-muted">Date de Fin (Optionnel)</label>
                            <input type="date" name="date_fin" id="date_fin" class="form-control form-control-sm" 
                                   value="{{ $historique->date_fin ? \Carbon\Carbon::parse($historique->date_fin)->format('Y-m-d') : '' }}">
                        </div>
                    </div>

                    {{-- Actions du formulaire --}}
                    <div class="text-end border-top pt-3 mt-3">
                        <a href="{{ route('historique_postes.show', $historique->matricule_employe) }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection