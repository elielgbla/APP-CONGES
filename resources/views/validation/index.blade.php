@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-content-center">
            <h5 class="mb-0"><i class="fas fa-stamp me-2"></i> Demandes en attente de votre Visa</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Agent</th>
                            <th>Service</th>
                            <th>Type Permission</th>
                            <th>Période</th>
                            <th>Statut Actuel</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($demandes as $demande)
                            <tr>
                                <td><span class="badge bg-secondary font-monospace">{{ $demande->code_demande }}</span></td>
                                <td><strong>{{ $demande->employe->nom }} {{ $demande->employe->prenoms }}</strong></td>
                                <td>{{ $demande->employe->service->libelle_service }}</td>
                                <td>{{ $demande->typePermission->libelle_type }}</td>
                                <td>
                                    Du {{ $demande->date_debut_prevu->format('d/m/Y') }}<br>
                                    au {{ $demande->date_fin_prevu->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($demande->statut_demande === 'en_attente_n1')
                                        <span class="badge bg-warning text-dark">Attente Visa Responsable (N1)</span>
                                    @elseif($demande->statut_demande === 'en_attente_n2')
                                        <span class="badge bg-info text-dark">Attente Visa RH (N2)</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('demandes.show', $demande->code_demande) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i> Ouvrire le dossier
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-check-circle fa-2x mb-2 text-success"></i><br>
                                    Aucun dossier en attente de validation pour votre profil.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $demandes->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
