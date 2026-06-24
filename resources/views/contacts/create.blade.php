@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('contacts.index') }}" class="btn btn-sm btn-outline-secondary me-3 py-1 px-2">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
                <h4 class="fw-bold mb-0 text-dark">Nouveau Contact Agent</h4>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="{{ route('contacts.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="matricule_employe" class="form-label small fw-bold text-muted">Attribuer à l'agent :</label>
                        <select name="matricule_employe" id="matricule_employe" class="form-select form-select-sm @error('matricule_employe') is-invalid @enderror" required>
                            <option value="" selected disabled>-- Sélectionner l'employé titulaire --</option>
                            @foreach($employes as $emp)
                                <option value="{{ $emp->matricule_employe }}" {{ old('matricule_employe') == $emp->matricule_employe ? 'selected' : '' }}>
                                    [{{ $emp->matricule_employe }}] {{ strtoupper($emp->nom) }} {{ $emp->prenom }}
                                </option>
                            @endforeach
                        </select>
                        @error('matricule_employe')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="numero_contact" class="form-label small fw-bold text-muted">Adresse Émail ou Numéro de Téléphone</label>
                        <input type="text" name="numero_contact" id="numero_contact" class="form-control form-control-sm @error('numero_contact') is-invalid @enderror" value="{{ old('numero_contact') }}" placeholder="Ex: +225 07 05 04 01 02 ou direction@sogetho.ci" required>
                        @error('numero_contact')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4 bg-light p-3 rounded border">
                        <label class="form-label small fw-bold text-dark mb-2 d-block">
                            <i class="fa-solid fa-tags text-muted me-1"></i> Usages et types de cette coordonnée (Sélection multiple) :
                        </label>

                        <div class="row g-2">
                            @foreach($types as $type)
                                <div class="col-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="types[]" value="{{ $type->code_type_contact }}" id="type_{{ $type->code_type_contact }}" {{ is_array(old('types')) && in_array($type->code_type_contact, old('types')) ? 'checked' : '' }}>
                                        <label class="form-check-label small text-dark fw-semibold" for="type_{{ $type->code_type_contact }}" style="cursor: pointer;">
                                            {{ $type->libelle_type_contact }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @error('types')
                            <div class="text-danger small mt-2 d-block" style="font-size: 0.75rem;">
                                <i class="fa-solid fa-triangle-exclamation"></i> Vous devez cocher au moins un usage.
                            </div>
                        @enderror
                    </div>

                    <div class="text-end border-top pt-3">
                        <a href="{{ route('contacts.index') }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">Enregistrer le contact</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection