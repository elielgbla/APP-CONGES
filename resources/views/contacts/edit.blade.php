@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="d-flex align-items-center mb-3">
                <a href="{{ route('contacts.index') }}" class="btn btn-sm btn-outline-secondary me-3 py-1 px-2">
                    <i class="fa-solid fa-arrow-left"></i> Retour
                </a>
                <h4 class="fw-bold mb-0 text-dark">Modifier la Coordonnée</h4>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <form action="{{ route('contacts.update', $contact->code_contact) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="matricule_employe" class="form-label small fw-bold text-muted">Agent titulaire</label>
                        <select name="matricule_employe" id="matricule_employe" class="form-select form-select-sm @error('matricule_employe') is-invalid @enderror" required>
                            @foreach($employes as $emp)
                                <option value="{{ $emp->matricule_employe }}" {{ old('matricule_employe', $contact->matricule_employe) === $emp->matricule_employe ? 'selected' : '' }}>
                                    [{{ $emp->matricule_employe }}] {{ strtoupper($emp->nom) }} {{ $emp->prenom }}
                                </option>
                            @endforeach
                        </select>
                        @error('matricule_employe')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="numero_contact" class="form-label small fw-bold text-muted">Numéro ou Adresse de contact</label>
                        <input type="text" name="numero_contact" id="numero_contact" class="form-control form-control-sm @error('numero_contact') is-invalid @enderror" value="{{ old('numero_contact', $contact->numero_contact) }}" required>
                        @error('numero_contact')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="code_type_contact" class="form-label small fw-bold text-muted">Type / Usage du contact</label>
                        <select name="code_type_contact" id="code_type_contact" class="form-select form-select-sm @error('code_type_contact') is-invalid @enderror" required>
                            @foreach($types as $type)
                                <option value="{{ $type->code_type_contact }}" {{ old('code_type_contact', $contact->code_type_contact) === $type->code_type_contact ? 'selected' : '' }}>
                                    {{ $type->libelle_type_contact }}
                                </option>
                            @endforeach
                        </select>
                        @error('code_type_contact')<div class="invalid-feedback small">{{ $message }}</div>@enderror
                    </div>

                    <div class="text-end border-top pt-3">
                        <a href="{{ route('contacts.index') }}" class="btn btn-sm btn-light px-3 me-2">Annuler</a>
                        <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold">Sauvegarder les modifications</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection