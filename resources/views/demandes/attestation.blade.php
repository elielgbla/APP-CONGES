<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attestation de congé - {{ $demande->code_demande }}</title>
    <style>
        body {
            color: #222;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 2rem;
        }
        .document {
            border: 1px solid #1e5631;
            margin: 0 auto;
            max-width: 800px;
            padding: 2.5rem;
        }
        .header, .footer {
            text-align: center;
        }
        .header {
            border-bottom: 2px solid #1e5631;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
        }
        h1 {
            color: #1e5631;
            font-size: 1.6rem;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
        }
        .reference, .muted {
            color: #666;
        }
        .content {
            margin: 2.5rem 0;
            text-align: justify;
        }
        .signatures {
            border-collapse: collapse;
            margin-top: 2.5rem;
            width: 100%;
        }
        .signatures th, .signatures td {
            border: 1px solid #bbb;
            padding: 0.65rem;
            text-align: left;
        }
        .signatures th {
            background: #f2f5f2;
        }
        .actions {
            margin: 1rem auto;
            max-width: 800px;
            text-align: right;
        }
        .actions button {
            background: #1e5631;
            border: 0;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            padding: 0.65rem 1rem;
        }
        @media print {
            body {
                padding: 0;
            }
            .document {
                border: 0;
                max-width: none;
                padding: 1cm;
            }
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="actions">
        <button type="button" onclick="window.print()">Imprimer l'attestation</button>
    </div>

    <main class="document">
        <header class="header">
            <h1>Attestation d'autorisation de congé</h1>
            <div class="reference">Référence : {{ $demande->code_demande }}</div>
        </header>

        <p>
            Je soussigné(e), représentant(e) de la SOGETHO-SA, atteste que
            <strong>{{ $demande->employe->nom ?? 'Employé' }} {{ $demande->employe->prenom ?? '' }}</strong>,
            matricule <strong>{{ $demande->matricule_employe }}</strong>,
            est autorisé(e) à s'absenter au titre de
            <strong>{{ $demande->typePermission->libelle_type_permission ?? 'congé' }}</strong>.
        </p>

        <section class="content">
            <p>
                La période autorisée s'étend du
                <strong>{{ \Illuminate\Support\Carbon::parse($demande->date_debut_prevu)->format('d/m/Y') }}</strong>
                au
                <strong>{{ \Illuminate\Support\Carbon::parse($demande->date_fin_prevu)->format('d/m/Y') }}</strong>
                inclus, soit <strong>{{ $demande->nombre_jours }} jour(s)</strong>.
            </p>
            <p>
                Motif déclaré : {{ $demande->motif }}
            </p>
        </section>

        <p>
            La demande a été approuvée le
            <strong>{{ optional($validations->get(4)?->date_decision)->format('d/m/Y') ?? 'date non renseignée' }}</strong>.
            La présente attestation est délivrée pour servir et valoir ce que de droit.
        </p>

        <table class="signatures">
            <thead>
                <tr>
                    <th>Niveau</th>
                    <th>Décisionnaire</th>
                    <th>Décision</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ([1 => 'Responsable de service', 2 => 'Ressources humaines', 3 => "Direction d'exploitation", 4 => 'Direction générale'] as $niveau => $libelle)
                    @php($validation = $validations->get($niveau))
                    <tr>
                        <td>{{ $libelle }}</td>
                        <td>
                            {{ $validation?->valideur
                                ? trim($validation->valideur->prenom . ' ' . $validation->valideur->nom)
                                : 'Non renseigné' }}
                        </td>
                        <td>{{ $validation?->decision === 'valide' ? 'Accordée' : 'Non renseignée' }}</td>
                        <td>{{ optional($validation?->date_decision)->format('d/m/Y') ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <footer class="footer muted">
            <p>Document généré le {{ now()->format('d/m/Y') }} — SOGETHO-SA</p>
        </footer>
    </main>
</body>
</html>
