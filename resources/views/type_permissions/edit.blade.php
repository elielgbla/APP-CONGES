@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('type-permissions.index') }}" class="btn btn-sm btn-outline-secondary me-3 py-1 px-2">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0 text-dark">Modifier le Type de Permission</h4>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="{{ route('type-permissions.update', $type->code_type_permission) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Code Référence (Clé Primaire)</label>
                        <input type="text" class="form-control form-control-sm bg-light text-muted fw-bold" value="{{ $type->code_type_permission }}" readonly disabled>
                        <div class="form-text text-warning" style="font-size: 0.75rem;"><i class="fa-solid fa-lock"></i> Non modifiable car lié à l'historique des données métier.</div>
                    </div>

                    <div class="mb-3">
                        <label for="libelle_type_permission" class="form-label small fw-bold text-muted">Libellé complet</label>
                        <input type="text" name="libelle_type_permission" id="libelle_type_permission" class="form-control form-control-sm @error('libelle_type_permission') is-invalid @enderror" value="{{ old('libelle_type_permission', $type->libelle_type_permission) }}" required>
                        @error('libelle_type_permission')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end">
                        <a href="{{ route('type-permissions.index') }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">Mettre à jour</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection
