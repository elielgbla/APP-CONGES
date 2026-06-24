<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SOGETHO-SA - Titre de Congé {{ $demande->code_demande }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
            background-color: #fff;
        }
        .print-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
        }
        .header-title {
            color: #1e5631;
            letter-spacing: 1px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                margin: 0;
                padding: 0;
            }
            .print-container {
                padding: 10px;
                max-width: 100%;
            }
        }
    </style>
</head>
<body onload="window.print();">

    <div class="print-container">
        
        <div class="text-end mb-4 no-print">
            <button onclick="window.print();" class="btn btn-sm btn-dark px-3">
                <i class="fa-solid fa-print me-1"></i> Relancer l'impression
            </button>
            <button onclick="window.close();" class="btn btn-sm btn-outline-danger px-2 ms-2">
                Fermer
            </button>
        </div>

        <div class="row align-items-center mb-4">
            <div class="col-6">
                <img src="{{ asset('images/palm_club_logo.png') }}" alt="PALM CLUB HOTEL" style="max-height: 65px; object-fit: contain;">
                <div class="text-success small fw-bold ps-2" style="font-size: 0.75rem; letter-spacing: 1px;">L'hospitalité ivoirienne</div>
            </div>
            <div class="col-6 text-end">
                <h1 class="fw-bold mb-0 header-title" style="font-size: 1.7rem;">SOGETHO-SA</h1>
            </div>
        </div>

        <hr style="border-top: 2px solid #1e5631;" class="mt-0 mb-4">

        <div class="row mb-5">
            <div class="col-7">
                <table class="table table-borderless table-sm mb-0">
                    <tr>
                        <td class="fw-bold text-muted" style="width: 130px; font-size: 0.9rem;">Nom & Prénoms :</td>
                        <td class="fw-bold text-uppercase" style="font-size: 0.95rem;">{{ $demande->employe->nom }} {{ $demande->employe->prenom }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted" style="font-size: 0.9rem;">Fonction :</td>
                        {{-- Accès sécurisé au poste actuel via la relation nettoyée d'Eloquent --}}
                        <td>{{ $demande->employe->poste->libelle_poste ?? 'Non définie' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted" style="font-size: 0.9rem;">Matricule :</td>
                        <td class="fw-bold text-danger">{{ $demande->employe->matricule_employe }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted" style="font-size: 0.9rem;">Service :</td>
                        {{-- Accès sécurisé au service actuel via la relation nettoyée d'Eloquent --}}
                        <td>{{ $demande->employe->service->libelle_service ?? 'Technique' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted" style="font-size: 0.9rem;">Contact :</td>
                        {{-- Récupération dynamique depuis la relation contacts si nécessaire, ou fallback --}}
                        <td>{{ $demande->employe->contacts->first()->valeur_contact ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-5 text-end">
                <p class="fw-bold mb-0">Abidjan, le {{ \Carbon\Carbon::parse($demande->date_demande)->translatedFormat('d F Y') }}</p>
                <div class="text-start offset-md-2 mt-4" style="font-size: 0.9rem;">
                    <span>/-)</span><br>
                    <strong class="text-dark">Monsieur le Directeur Général de la SOGETHO-SA</strong>
                </div>
            </div>
        </div>

        <div class="mb-5">
            <p class="mb-4"><span class="fw-bold text-decoration-underline">OBJET</span> : <span class="fw-bold">Demande de {{ $demande->typePermission->libelle_type_permission ?? 'congé annuel' }}</span></p>
            
            <p class="mt-4">Monsieur le Directeur Général,</p>
            <p class="text-justify mb-3">
                Je viens par la présente, solliciter auprès de votre haute bienveillance l'autorisation d'un congé d'un mois.
            </p>
            
            <p class="text-justify">
                Je propose, avec votre accord la période allant du 
                <span class="fw-bold">{{ \Carbon\Carbon::parse($demande->date_debut_prevu)->translatedFormat('d F Y') }}</span> 
                au 
                <span class="fw-bold">{{ \Carbon\Carbon::parse($demande->date_fin_prevu)->translatedFormat('d F Y') }} inclus</span>.
            </p>
            
            <p class="text-muted small my-2">
                (Durée totale décomptée : <strong>{{ \Carbon\Carbon::parse($demande->date_debut_prevu)->diffInDays(\Carbon\Carbon::parse($demande->date_fin_prevu)) + 1 }} jour(s)</strong>)
            </p>

            <p class="text-justify mt-3">
                Toutefois, je reste disponible, en cas de nécessité, pour tous travaux durant cette période.
            </p>
            
            <p class="mt-4">
                Dans l'attente d'une suite favorable, je vous prie d'agréer, Monsieur le Directeur Général, mes salutations distinguées.
            </p>
        </div>

        <div class="mt-5 pt-3">
            <div class="row text-center mb-5">
                <div class="col-6">
                    <p class="fw-bold text-decoration-underline mb-5">L'INTÉRESSÉ(E)</p>
                    <div class="text-muted small mt-4">(Signature de l'agent)</div>
                </div>
                <div class="col-6">
                    <p class="fw-bold text-decoration-underline mb-4">LE CHEF DE SERVICE</p>
                    <div class="text-start mx-auto p-2 border rounded bg-light bg-opacity-50 small text-muted style-review" style="max-width: 280px; font-size: 0.75rem;">
                        Visa N1 : {{ $demande->statut_demande !== 'en_attente_n1' ? 'ACCORDÉ' : 'EN ATTENTE' }}
                    </div>
                </div>
            </div>

            <div class="row text-center mb-5">
                <div class="col-6">
                    <p class="fw-bold text-decoration-underline mb-4">RESPONSABLE DES RESSOURCES HUMAINES</p>
                    <div class="text-start mx-auto p-2 border rounded bg-light bg-opacity-50 small text-muted style-review" style="max-width: 280px; font-size: 0.75rem;">
                        Visa N2 : {{ in_array($demande->statut_demande, ['en_attente_n3', 'en_attente_n4', 'approuvee']) ? 'ACCORDÉ' : 'EN ATTENTE' }}
                    </div>
                </div>
                <div class="col-6">
                    <p class="fw-bold text-decoration-underline mb-4">DIRECTEUR D'EXPLOITATION</p>
                    <div class="text-start mx-auto p-2 border rounded bg-light bg-opacity-50 small text-muted style-review" style="max-width: 280px; font-size: 0.75rem;">
                        Visa N3 : {{ in_array($demande->statut_demande, ['en_attente_n4', 'approuvee']) ? 'ACCORDÉ' : 'EN ATTENTE' }}
                    </div>
                </div>
            </div>

            <div class="row text-center mt-4">
                <div class="col-12">
                    <p class="fw-bold text-decoration-underline mb-3">DÉCISION DU DIRECTEUR GÉNÉRAL</p>
                    <div class="d-inline-block p-3 border border-dark rounded bg-light fw-bold text-uppercase style-review" style="min-width: 320px; font-size: 0.85rem; border-style: dashed !important;">
                        @if($demande->statut_demande === 'approuvee')
                            <span class="text-success"><i class="fa-solid fa-check-double"></i> Demande Approuvée & Validée</span>
                        @elseif($demande->statut_demande === 'rejetee')
                            <span class="text-danger"><i class="fa-solid fa-xmark"></i> Demande Rejetée</span>
                        @else
                            <span class="text-muted"><i class="fa-solid fa-hourglass-half"></i> Dossier en cours d'instruction</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>