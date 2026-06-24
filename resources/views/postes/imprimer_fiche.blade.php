<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>SOGETHO-SA — Corps d'emploi {{ $poste->code_poste }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 13px; padding: 20px; }
        @media print { .no-print { display: none !important; } body { margin: 10mm; } }
    </style>
</head>
<body onload="window.print();">

    <div class="text-end mb-3 no-print">
        <button onclick="window.print();" class="btn btn-sm btn-dark">Imprimer</button>
        <button onclick="window.close();" class="btn btn-sm btn-outline-danger">Fermer</button>
    </div>

    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-4">
        <div>
            <h4 class="fw-bold mb-0 text-success">SOGETHO-SA</h4>
            <small class="text-secondary">PALM CLUB — ABIDJAN</small>
        </div>
        <div class="text-end">
            <h5 class="fw-bold mb-0 text-secondary">AFFECTATION PAR CORPS D'EMPLOI</h5>
            <small class="text-muted">Généré le : {{ now()->format('d/m/Y H:i') }}</small>
        </div>
    </div>

    <div class="bg-light p-3 border rounded mb-3">
        <h6>QUALIFICATION VADE-MECUM : <strong>{{ strtoupper($poste->libelle_poste) }} ({{ $poste->code_poste }})</strong></h6>
    </div>

    <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-secondary">
            <tr>
                <th style="width: 20%;">Matricule</th>
                <th style="text-align: left; width: 50%;">Nom & Prénoms de l'agent</th>
                <th style="text-align: left; width: 30%;">Service d'affectation</th>
            </tr>
        </thead>
        <tbody>
            {{-- Pas de @stop fantôme ici, tout est propre ! --}}
            @forelse($poste->employes as $agent)
                <tr>
                    <td class="fw-bold text-danger">{{ $agent->matricule_employe }}</td>
                    <td class="text-start text-uppercase fw-bold">{{ $agent->nom }} {{ $agent->prenom }}</td>
                    <td class="text-start">{{ $agent->service->libelle_service ?? 'Non spécifié' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-muted py-3">Aucun agent répertorié sous ce poste.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-3 text-end text-muted small">
        Effectif total certifié : <strong>{{ $poste->employes->count() }}</strong>
    </div>
</body>
</html>