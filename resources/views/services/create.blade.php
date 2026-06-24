@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('services.index') }}" class="btn btn-sm btn-outline-secondary me-3 py-1 px-2">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <h4 class="fw-bold mb-0 text-dark">Nouveau Département</h4>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="{{ route('services.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="code_service" class="form-label small fw-bold text-muted">Code Service (Identifiant unique)</label>
                        <input type="text" name="code_service" id="code_service" class="form-control form-control-sm @error('code_service') is-invalid @enderror" value="{{ old('code_service') }}" placeholder="Ex: SERV_COMPTA, DIR_TECH" required autocomplete="off">
                        @error('code_service')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="libelle_service" class="form-label small fw-bold text-muted">Nom explicite de la direction</label>
                        <input type="text" name="libelle_service" id="libelle_service" class="form-control form-control-sm @error('libelle_service') is-invalid @enderror" value="{{ old('libelle_service') }}" placeholder="Ex: Comptabilité et Finances" required>
                        @error('libelle_service')
                            <div class="invalid-feedback small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-end border-top pt-3">
                        <a href="{{ route('services.index') }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">Enregistrer la structure</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection