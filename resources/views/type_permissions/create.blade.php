@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('type-permissions.index') }}" class="btn btn-sm btn-outline-secondary me-3 py-1 px-2">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0 text-dark">Nouveau Type de Permission</h4>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="{{ route('type-permissions.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="code_type_permission" class="form-label small fw-bold text-muted">Code Référence (Unique)</label>
                        <input type="text" name="code_type_permission" id="code_type_permission" class="form-control form-control-sm @error('code_type_permission') is-invalid @enderror" value="{{ old('code_type_permission') }}" placeholder="Ex: CONGE_EX, EXP_MAL" required autocomplete="off">
                        <div class="form-text text-muted" style="font-size: 0.75rem;">Il sera converti automatiquement en majuscules dans le système.</div>
                        @error('code_type_permission')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="libelle_type_permission" class="form-label small fw-bold text-muted">Libellé complet</label>
                        <input type="text" name="libelle_type_permission" id="libelle_type_permission" class="form-control form-control-sm @error('libelle_type_permission') is-invalid @enderror" value="{{ old('libelle_type_permission') }}" placeholder="Ex: Congé Exceptionnel pour Événement" required>
                        @error('libelle_type_permission')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('type-permissions.index') }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">Enregistrer le Type</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection