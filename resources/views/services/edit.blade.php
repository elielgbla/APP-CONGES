@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-secondary me-3 py-1 px-2">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
                <h4 class="fw-bold mb-0 text-dark">Modifier le Département</h4>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="{{ route('services.update', $service->code_service) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Code Structure (Clé Primaire)</label>
                        <input type="text" class="form-control form-control-sm bg-light text-muted fw-bold" value="{{ $service->code_service }}" readonly disabled>
                        <div class="form-text text-warning" style="font-size: 0.75rem;"><i class="fa-solid fa-lock"></i> Verrouillé pour préserver les affectations de contrats.</div>
                    </div>

                    <div class="mb-4">
                        <label for="libelle_service" class="form-label small fw-bold text-muted">Nom explicite de la direction</label>
                        <input type="text" name="libelle_service" id="libelle_service" class="form-control form-control-sm @error('libelle_service') is-invalid @enderror" value="{{ old('libelle_service', $service->libelle_service) }}" required>
                        @error('libelle_service')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end border-top pt-3">
                        <a href="{{ route('services.index') }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">Mettre à jour</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection