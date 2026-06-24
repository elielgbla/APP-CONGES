@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('postes.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="fa-solid fa-arrow-left"></i></a>
                <h4 class="fw-bold mb-0 text-dark">Nouveau Poste de Travail</h4>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="{{ route('postes.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="code_poste" class="form-label small fw-bold text-muted">Code Référence</label>
                        <input type="text" name="code_poste" id="code_poste" class="form-control form-control-sm @error('code_poste') is-invalid @enderror" placeholder="Ex: DESIGNER, ASSIST_RH" required autocomplete="off">
                        @error('code_poste')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="libelle_poste" class="form-label small fw-bold text-muted">Désignation de la fonction</label>
                        <input type="text" name="libelle_poste" id="libelle_poste" class="form-control form-control-sm @error('libelle_poste') is-invalid @enderror" placeholder="Ex: Concepteur Graphique" required>
                        @error('libelle_poste')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div class="text-end border-top pt-3">
                        <a href="{{ route('postes.index') }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">Valider la fonction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection