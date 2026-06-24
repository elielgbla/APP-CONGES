<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SOGETHO-SA - Effectifs {{ $service->code_service }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 13px; color: #333; padding: 20px; }
        .title-header { color: #1e5631; text-transform: uppercase; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 10mm; }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="text-end mb-3 no-print">
        <button onclick="window.print();" class="btn btn-sm btn-dark">Relancer l'impression</button>
        <button onclick="window.close();" class="btn btn-sm btn-outline-danger">Fermer</button>
    </div>

    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0 title-header">SOGETHO-SA</h4>
            <small class="text-success fw-bold">PALM CLUB HÔTEL — ABIDJAN</small>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-0 text-muted">LISTE ÉMARGINAL DES EFFECTIFS</h5>
            <small class="text-secondary">Édité le : {{ now()->format('d/m/Y H:i') }}</small>
        </div>
    </div>

    <div class="mb-4 bg-light p-3 border rounded">
        <h6 class="fw-bold text-dark mb-1">STRUCTURE : {{ strtoupper($service->libelle_service) }} ({{ $service->code_service }})</h6>
        @if($chefService)
            <p class="mb-0 text-muted">Responsable N1 désigné : <strong>{{ strtoupper($chefService->nom) }} {{ $chefService->prenom }}</strong> ({{ $chefService->matricule_employe }})</p>
        @else
            <p class="mb-0 text-muted italic">Aucun responsable N1 affecté à ce jour.</p>
        @endif
    </div>

    <table class="table table-bordered table-striped align-middle text-center mt-3">
        <thead class="table-secondary">
            <tr>
                <th style="width: 15%;">Matricule</th>
                <th style="text-align: left; width: 45%;">Nom & Prénoms de l'agent</th>
                <th style="text-align: left; width: 25%;">Poste occupé</th>
                <th style="width: 15%;">Émargement</th>
            </tr>
        </thead>
        <tbody>
           
            @forelse($service->employes as $agent)
                <tr>
                    <td class="fw-bold text-danger">{{ $agent->matricule_employe }}</td>
                    <td class="text-start text-uppercase fw-bold">{{ $agent->nom }} {{ $agent->prenom }}</td>
                    <td class="text-start">{{ $agent->poste->libelle_poste ?? 'Non spécifié' }}</td>
                    <td style="height: 40px;"></td> </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-muted py-3">Aucun agent répertorié dans ce service.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4 text-end text-muted small">
        Nombre total d'agents validés : <strong>{{ $service->employes->count() }}</strong>
    </div>

    <div class="row mt-5 pt-3">
        <div class="col-4 offset-8 text-center border-top pt-2 small text-muted fw-bold">
            Visa Direction RH SOGETHO
        </div>
    </div>

</body>
</html>
