<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SOGETHO-SA - Registre des Congés et Permissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header-title {
            color: #1e5631;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th {
            background-color: #f8f9fa !important;
            color: #495057;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 20px; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="text-end mb-3 no-print">
        <button onclick="window.print();" class="btn btn-sm btn-dark">
            Relancer l'impression
        </button>
    </div>

    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
        <div>
            <h5 class="fw-bold mb-0 text-uppercase header-title">SOGETHO-SA</h5>
            <small class="text-success fw-bold">PALM CLUB HÔTEL — Abidjan</small>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-0 text-muted">REGISTRE DES CONGÉS</h5>
            <small class="text-secondary">Édité le : {{ now()->format('d/m/Y H:i') }}</small>
            @if(!empty($statut) && $statut !== 'tous')
                <br><span class="badge bg-secondary mt-1">Filtre : {{ strtoupper(str_replace('_', ' ', $statut)) }}</span>
            @endif
        </div>
    </div>

    <table class="table table-bordered table-striped align-middle text-center">
        <thead>
            <tr>
                <th>Code</th>
                <th>Matricule</th>
                <th>Agent & Service</th>
                <th>Type</th>
                <th>Date Début</th>
                <th>Date Fin</th>
                <th>Durée</th>
                <th>Statut Actuel</th>
            </tr>
        </thead>
        <tbody>
            @forelse($demandes as $demande)
                <tr>
                    <td class="fw-bold text-secondary">{{ $demande->code_demande }}</td>
                    <td class="fw-bold">{{ $demande->matricule_employe }}</td>
                    <td class="text-start">
                        <div class="fw-bold">{{ $demande->employe->nom }} {{ $demande->employe->prenom }}</div>
                        {{-- Accès direct et sécurisé au service actuel via la relation nettoyée --}}
                        <small class="text-muted">{{ $demande->employe->service->libelle_service ?? 'Non assigné' }}</small>
                    </td>
                    <td>{{ $demande->typePermission->libelle_type_permission ?? 'Congé' }}</td>
                    <td>{{ \Carbon\Carbon::parse($demande->date_debut_prevu)->format('d/m/Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($demande->date_fin_prevu)->format('d/m/Y') }}</td>
                    <td class="fw-bold">
                        {{ \Carbon\Carbon::parse($demande->date_debut_prevu)->diffInDays(\Carbon\Carbon::parse($demande->date_fin_prevu)) + 1 }} j
                    </td>
                    <td>
                        <span class="small fw-bold text-uppercase">
                            {{ str_replace('_', ' ', $demande->statut_demande) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-muted py-4">Aucune demande enregistrée ou correspondante à ce statut.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="row mt-5 pt-4 text-center small text-muted">
        <div class="col-4 border-top pt-2">Service RH</div>
        <div class="col-4 offset-4 border-top pt-2">Direction Générale</div>
    </div>

</body>
</html>