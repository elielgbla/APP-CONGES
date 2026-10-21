@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm border-0 p-4 bg-white">
                
                <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                    <h5 class="fw-bold mb-0 text-dark">
                        <i class="fa-solid fa-address-book text-muted me-2"></i>Fiche Contacts de l'Agent
                    </h5>
                    <a href="{{ route('contacts.index') }}" class="btn btn-sm btn-light border px-3">
                        <i class="fa-solid fa-arrow-left me-1"></i> Répertoire
                    </a>
                </div>

                <div class="p-3 bg-light rounded border mb-4">
                    <div class="row small text-muted">
                        <div class="col-sm-6 mb-2 mb-sm-0">
                            <span class="d-block text-uppercase" style="font-size: 0.72rem;">Agent Titulaire :</span>
                            <strong class="text-dark fs-6">{{ strtoupper($employe->nom) }} {{ $employe->prenom }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="d-block text-uppercase" style="font-size: 0.72rem;">Matricule & Poste :</span>
                            <strong class="text-danger">{{ $employe->matricule_employe }}</strong> 
                            <span class="text-secondary">({{ $employe->poste->libelle_poste ?? 'Aucun poste' }})</span>
                        </div>
                    </div>
                </div>

                <label class="form-label small fw-bold text-muted d-block mb-2">
                    <i class="fa-solid fa-layer-group me-1"></i> Canaux et fonctionnalités réseau enregistrés :
                </label>

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle border mb-0 small">
                        <thead class="table-light text-secondary">
                            <tr>
                                <th>Réf Ligne</th>
                                <th>Coordonnée / Numéro</th>
                                <th>Usage / Description du Type</th>
                                <th class="text-center" style="width: 15%;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allContactsAgent as $item)
                                <tr>
                                    <td class="text-muted fw-bold" style="font-size: 0.75rem;">
                                        {{ $item->code_contact ?? $item->id_contact }}
                                    </td>
                                    <td class="fw-bold text-dark">
                                        {{-- Correction de numero_contact vers valeur_contact --}}
                                        {{ $item->valeur_contact ?? $item->numero_contact }}
                                    </td>
                                    <td>
                                        {{-- Utilisation du Nullsafe ?-> pour eviter l'erreur sur null --}}
                                        {{ $item->typeContact?->description ?? $item->type?->description ?? $item->typeContact?->libelle_type_contact ?? $item->type?->libelle_type_contact ?? 'Non défini' }}
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('contacts.edit', $item->code_contact ?? $item->id_contact) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Modifier cette attribution">
                                                <i class="fa-solid fa-pen-to-square" style="font-size: 0.75rem;"></i>
                                            </a>
                                            <form action="{{ route('contacts.destroy', $item->code_contact ?? $item->id_contact) }}" method="POST" onsubmit="return confirm('Supprimer cet usage précis pour ce numéro ?');" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Supprimer cet usage">
                                                    <i class="fa-solid fa-trash-can" style="font-size: 0.75rem;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted text-center py-3">Aucune coordonnée active pour cet agent.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection