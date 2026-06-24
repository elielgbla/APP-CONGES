@extends('layouts.app')

@section('content')
<div class="container my-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">Types de Permissions</h1>
            <p class="text-muted small mb-0">Configurations des motifs de référence pour les demandes de congés à la SOGETHO-SA.</p>
        </div>
        <a href="{{ route('type-permissions.create') }}" class="btn btn-sm btn-success px-3">
            <i class="fa-solid fa-plus me-1"></i> Ajouter un type
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm small py-2 mb-3">
            <i class="fa-solid fa-circle-check me-1"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm small py-2 mb-3">
            <i class="fa-solid fa-circle-xmark me-1"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0 small">
                <thead class="table-light text-secondary">
                    <tr>
                        <th style="width: 25%;">Code Référence</th>
                        <th style="text-align: left; width: 45%;">Libellé du Type de Permission</th>
                        <th style="width: 15%;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($types as $type)
                        <tr>
                            <td class="fw-bold text-secondary text-uppercase">
                                <span class="badge bg-light text-dark border px-2 py-1">{{ $type->code_type_permission }}</span>
                            </td>
                            <td class="text-start fw-bold text-dark">
                                {{ $type->libelle_type_permission }}
                            </td>

                            <td>
    <div class="d-flex justify-content-center gap-1">
        <a href="{{ route('type-permissions.show', $type->code_type_permission) }}" class="btn btn-sm btn-outline-info py-0 px-2" title="Voir">
            <i class="fa-solid fa-eye"></i> Voir
        </a>

        <a href="{{ route('type-permissions.edit', $type->code_type_permission) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Modifier">
            <i class="fa-solid fa-pen-to-square"></i> Modif
        </a>

        <form action="{{ route('type-permissions.destroy', $type->code_type_permission) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce type de référence ?');" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Supprimer">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </form>
    </div>
</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-muted py-4">Aucun type de permission enregistré pour le moment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
