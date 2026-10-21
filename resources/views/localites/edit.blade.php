@extends('layouts.app')

@section('content')
<div class="container py-4 pb-5">
    
    <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom" style="border-color: #e8e5e1 !important;">
        <div>
            <h1 class="h2 fw-bold text-brown" style="color: #4a3b32;">Modifier la Localité</h1>
            <p class="text-muted mb-0">Mise à jour des informations de : <span class="fw-bold text-dark">{{ $localite->libelle_localite }}</span></p>
        </div>
        <div>
            <a href="{{ route('localites.index') }}" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 6px; font-weight: 500;">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour au registre
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0 mx-auto bg-white mt-4" style="max-width: 600px; border-radius: 12px;">
        <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4 px-md-5">
            <h5 class="fw-bold text-dark d-flex align-items-center mb-0">
                <span class="bg-warning text-dark rounded-circle d-inline-flex justify-content-center align-items-center me-3" style="width: 32px; height: 32px; font-size: 0.9rem;">
                    <i class="fa-solid fa-pen"></i>
                </span>
                Formulaire de mise à jour
            </h5>
        </div>

        <div class="card-body p-4 p-md-5 pt-4">
            
            <form action="{{ route('localites.update', $localite->code_localite) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="libelle_localite" class="form-label fw-bold small text-dark">
                        Libellé de la localité <span class="text-danger">*</span>
                    </label>
                    <div class="input-group input-group-lg shadow-sm" style="border-radius: 8px; overflow: hidden;">
                        <span class="input-group-text bg-light border-end-0" style="border-color: #ced4da;">
                            <i class="fa-solid fa-map-pin text-muted"></i>
                        </span>
                        <input type="text" 
                               name="libelle_localite" 
                               id="libelle_localite" 
                               class="form-control border-start-0 @error('libelle_localite') is-invalid @enderror" 
                               value="{{ old('libelle_localite', $localite->libelle_localite) }}" 
                               required 
                               maxlength="100">
                    </div>
                    
                    @error('libelle_localite')
                        <div class="text-danger small fw-semibold mt-2">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-3 mt-5 pt-3 border-top" style="border-color: #f2eee9 !important;">
                    <a href="{{ route('localites.index') }}" class="btn btn-light px-4 py-2" style="border-radius: 8px; font-weight: 500; color: #555;">
                        Annuler
                    </a>
                    <button type="submit" class="btn text-white px-4 py-2" style="background-color: #2e7d32; border-radius: 8px; font-weight: 500;">
                        Enregistrer les modifications <i class="fa-solid fa-floppy-disk ms-2"></i>
                    </button>
                </div>
            </form>
            
        </div>
    </div>
</div>
@endsection