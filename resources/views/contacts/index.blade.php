@extends('layouts.app')

@section('content')
<div class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Répertoire Téléphonique</h1>
            <p class="text-muted small mb-0">Liste du personnel et leurs canaux de communication actifs.</p>
        </div>
        <a href="{{ route('contacts.create') }}" class="btn btn-sm btn-success px-3">
            <i class="fa-solid fa-plus me-1"></i> Enregistrer un contact
        </a>
    </div>

    <div class="row mb-3">
        <div class="col-md-5">
            <form action="{{ route('contacts.index') }}" method="GET" class="d-flex gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Rechercher un agent ou un numéro..." value="{{ $search }}">
                    @if(!empty($search))
                        <a href="{{ route('contacts.index') }}" class="btn btn-outline-secondary bg-white border-start-0 text-muted px-2">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
                <button type="submit" class="btn btn-sm btn-dark px-3">Filtrer</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm small py-2 mb-3">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 small">
                <thead class="table-light text-secondary text-center">
                    <tr>
                        <th style="width: 20%; text-align: left;">Matricule</th>
                        <th style="width: 35%; text-align: left;">Agent Titulaire</th>
                        <th style="width: 35%;">Liste des Coordonnées</th>
                        <th style="width: 10%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employes as $emp)
                        <tr>
                            <td class="text-center text-muted fw-bold">
                                {{ $emp->matricule_employe }}
                            </td>
                            <td>
                                <strong class="text-dark d-block">{{ strtoupper($emp->nom) }} {{ $emp->prenom }}</strong>
                                <span class="text-muted" style="font-size: 0.75rem;">{{ $emp->poste->libelle_poste ?? 'Aucun poste' }}</span>
                            </td>
                            <td class="text-center">
                                @foreach($emp->contacts->unique('numero_contact') as $contact)
                                    <span class="badge bg-light text-dark border px-2 py-1 mx-1 my-1 rounded">
                                        @if($contact->type)
                                            @if(str_contains(strtoupper($contact->code_type_contact), 'TEL'))
                                                <i class="fa-solid fa-phone me-1 text-primary" style="font-size: 0.7rem;"></i>
                                            @elseif(str_contains(strtoupper($contact->code_type_contact), 'WHATSAPP'))
                                                <i class="fa-brands fa-whatsapp me-1 text-success fw-bold" style="font-size: 0.7rem;"></i>
                                            @else
                                                <i class="fa-solid fa-envelope me-1 text-warning" style="font-size: 0.7rem;"></i>
                                            @endif
                                        @endif
                                        {{ $contact->numero_contact }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                <a href="{{ route('contacts.show', $emp->contacts->first()->code_contact) }}" class="btn btn-sm btn-outline-secondary py-1 px-2" title="Voir les détails">
                                    <i class="fa-solid fa-eye"></i> Voir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted text-center py-4">Aucun agent avec un contact enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection