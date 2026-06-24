@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center">
                    <a href="{{ route('type-permissions.index') }}" class="btn btn-sm btn-outline-secondary me-3 py-1 px-2">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                    <h4 class="fw-bold mb-0 text-dark">Fiche Type Permission</h4>
                </div>
                <a href="{{ route('type-permissions.edit', $type->code_type_permission) }}" class="btn btn-sm btn-primary px-3">
                    <i class="fa-solid fa-pen-to-square me-1"></i> Modifier
                </a>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <div class="border-bottom pb-2 mb-3">
                    <span class="text-muted small uppercase fw-bold d-block">Identifiant de référence :</span>
                    <strong class="h5 text-secondary text-uppercase">{{ $type->code_type_permission }}</strong>
                </div>
                
                <div class="mb-3">
                    <span class="text-muted small fw-bold d-block">Nom du motif :</span>
                    <span class="text-dark fw-bold">{{ $type->libelle_type_permission }}</span>
                </div>

               
            </div>

        </div>
    </div>
</div>
@endsection