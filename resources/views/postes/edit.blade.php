@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('postes.index') }}" class="btn btn-sm btn-outline-secondary me-3"><i class="fa-solid fa-arrow-left"></i></a>
                <h4 class="fw-bold mb-0 text-dark">Modifier la Qualification</h4>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="{{ route('postes.update', $poste->code_poste) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Code Unique</label>
                        <input type="text" class="form-control form-control-sm bg-light text-muted fw-bold" value="{{ $poste->code_poste }}" readonly disabled>
                    </div>

                    <div class="mb-4">
                        <label for="libelle_poste" class="form-label small fw-bold text-muted">Désignation de la fonction</label>
                        <input type="text" name="libelle_poste" id="libelle_poste" class="form-control form-control-sm @error('libelle_poste') is-invalid @enderror" value="{{ old('libelle_poste', $poste->libelle_poste) }}" required>
                        @error('libelle_poste')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div class="text-end border-top pt-3">
                        <a href="{{ route('postes.index') }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection