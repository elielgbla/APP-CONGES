@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5 no-print-container">

<!-- En-tête de page -->
    <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom d-print-none" style="border-color: #e8e5e1 !important;">
        <div>
            <a href="{{ route('employes.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="color: #4a3b32; border-color: #dcd7ce;">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour à l'effectif
            </a>
            <h1 class="h2 fw-bold text-brown">Fiche Professionnelle</h1>
            <p class="text-muted mb-0">Dossier administratif numérique et historique de l'agent.</p>
        </div>

        <div>
            <button onclick="window.print();" class="btn btn-sm btn-success fw-medium px-3 me-2">
                <i class="fa-solid fa-print me-1"></i> Imprimer la fiche
            </button>
            <a href="{{ route('employes.edit', $employe->matricule_employe) }}" class="btn btn-sm btn-warning fw-medium px-3 me-2">
                <i class="fa-solid fa-user-pen me-1"></i> Modifier
            </a>
        </div>
    </div>

    <div class="print-area asset-a4">
        <!-- En-tête Impression -->
        <div class="print-header d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/palm_club_logo.png') }}" alt="Logo PALM CLUB HOTEL" class="bg-white rounded p-1" style="width: 80px; height: 80px; object-fit: contain; border: 1px solid #ddd;">
                <div>
                    <h4 class="fw-bold text-uppercase mb-0 text-brown" style="letter-spacing: 1px;">PALM CLUB HOTEL</h4>
                    <span class="small text-muted fw-semibold">SOGETHO-SA</span>
                </div>
            </div>
            <div class="text-end">
                <h3 class="fw-bold text-uppercase text-success mb-1" style="font-size: 1.4rem;">Fiche Individuelle</h3>
                <div class="badge bg-dark text-white font-monospace p-2">Matricule : {{ $employe->matricule_employe }}</div>
            </div>
        </div>

        <!-- État Civil -->
        <div class="row g-3 mb-4">
            <div class="col-9">
                <div class="card h-100 border-0 bg-light p-3">
                    <h5 class="text-brown border-bottom pb-2 mb-3 text-uppercase small fw-bold"><i class="fa-solid fa-user me-2"></i>État Civil</h5>
                    <div class="row g-3">
                        <div class="col-6">
                            <span class="small text-muted d-block">Nom & Prénoms :</span>
                            <strong class="text-dark fs-5">{{ strtoupper($employe->nom) }} {{ $employe->prenom }}</strong>
                        </div>
                        <div class="col-6">
                            <span class="small text-muted d-block">Genre :</span>
                            <span class="fw-semibold">{{ $employe->genre->libelle_genre ?? 'N/A' }}</span>
                        </div>
                        <div class="col-6">
                            <span class="small text-muted d-block">Date & Lieu de Naissance :</span>
                            <span class="fw-semibold">
                                {{ $employe->date_naissance ? \Carbon\Carbon::parse($employe->date_naissance)->format('d/m/Y') : 'Non renseignée' }} 
                                ({{ $employe->localiteNaissance->libelle_localite ?? $employe->code_localite_naissance }})
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="small text-muted d-block">Catégorie :</span>
                            <span class="badge bg-primary px-2 py-1">{{ $employe->categorie->libelle_categorie ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Avatar / Photo -->
            <div class="col-3 text-center">
                <div class="card h-100 border d-flex align-items-center justify-content-center p-2 bg-white">
                    @if($employe->photo && file_exists(public_path('storage/' . $employe->photo)))
                        <img src="{{ asset('storage/' . $employe->photo) }}" alt="Photo Agent" class="rounded border img-fluid" style="max-height: 140px; object-fit: cover;">
                    @else
                        <div class="rounded bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center fw-bold border border-success"
                             style="width: 110px; height: 140px; font-size: 3rem;">
                            {{ substr($employe->nom, 0, 1) }}
                        </div>
                    @endif
                    <div class="small fw-bold text-muted mt-2">AGENT</div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card border p-3 h-100 bg-white">
                    <h6 class="text-brown fw-bold border-bottom pb-1 mb-2 text-uppercase small"><i class="fa-solid fa-phone me-2"></i>Contacts & Coordonnées</h6>
                    <table class="table table-sm table-borderless mb-0 small">
                        <thead>
                            <tr class="border-bottom text-muted">
                                <th>Type</th>
                                <th>Numéro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employe->contacts as $contact)
                                <tr>
                                    <td><strong>{{ $contact->typeContact->libelle_type_contact ?? $contact->code_type_contact }} :</strong></td>
                                    <td class="font-monospace">{{ $contact->numero_contact }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted italic">Aucun contact enregistré</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border p-3 h-100 bg-white">
                    <h6 class="text-brown fw-bold border-bottom pb-1 mb-2 text-uppercase small"><i class="fa-solid fa-house-user me-2"></i>Adresses de Résidence</h6>
                    <table class="table table-sm table-borderless mb-0 small">
                        <thead>
                            <tr class="border-bottom text-muted">
                                <th>Réf</th>
                                <th>Adresse / Localité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employe->residences as $residence)
                                <tr>
                                    <td class="font-monospace">{{ $residence->code_residence }}</td>
                                    <td>{{ $residence->adresse }} ({{ $residence->localite->libelle_localite ?? $residence->code_localite }})</td>
                                </tr>
                            @empty
                                <tr><td colspan="2" class="text-muted italic">Aucune adresse enregistrée</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="alert alert-success d-flex justify-content-between align-items-center py-2 px-3 mb-4 border-success bg-success bg-opacity-10" style="border-radius: 6px;">
            <span class="fw-bold text-success text-uppercase small"><i class="fa-solid fa-calendar-check me-2"></i>Droits aux congés restants :</span>
            <span class="badge bg-success fs-6 px-3">{{ $employe->total_conge_restant ?? 0 }} Jours Disponibles</span>
        </div>

        <div class="mb-4 page-break-inside-prevent">
            <h6 class="text-brown fw-bold border-bottom pb-1 mb-2 text-uppercase small"><i class="fa-solid fa-briefcase me-2"></i>Historique des Postes & Affectations</h6>
            <table class="table table-sm table-bordered bg-white text-center small align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Poste</th>
                        <th>Service / Direction</th>
                        <th>Date Début</th>
                        <th>Date Fin</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employe->historiquePostes->sortByDesc('date_debut') as $histo)
                        <tr>
                            <td class="font-monospace text-muted">{{ $histo->code_historique }}</td>
                            <td><strong>{{ $histo->poste->libelle_poste ?? $histo->code_poste }}</strong></td>
                            <td>{{ $histo->service->libelle_service ?? $histo->code_service }}</td>
                            <td>{{ \Carbon\Carbon::parse($histo->date_debut)->format('d/m/Y') }}</td>
                            <td>{{ $histo->date_fin ? \Carbon\Carbon::parse($histo->date_fin)->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if(is_null($histo->date_fin))
                                    <span class="badge bg-success bg-opacity-25 text-success border border-success px-2 py-0">Poste Actuel</span>
                                @else
                                    <span class="text-muted small">Terminé</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted p-3">Aucun historique de poste trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mb-4 page-break-inside-prevent">
            <h6 class="text-brown fw-bold border-bottom pb-1 mb-2 text-uppercase small"><i class="fa-solid fa-envelope-open-text me-2"></i>Historique des Demandes de Permissions & Congés</h6>
            <table class="table table-sm table-bordered bg-white text-center small align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Réf</th>
                        <th>Date Demande</th>
                        <th>Type</th>
                        <th>Période Prévue</th>
                        <th>Motif</th>
                        <th>Statut Décision</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employe->demandes->sortByDesc('date_demande') as $demande)
                        <tr>
                            <td class="font-monospace small fw-bold">{{ $demande->code_demande }}</td>
                            <td>{{ \Carbon\Carbon::parse($demande->date_demande)->format('d/m/Y') }}</td>
                            <td><span class="fw-semibold">{{ $demande->typePermission->libelle_type_permission ?? $demande->code_type_permission }}</span></td>
                            <td class="small">
                                Du {{ \Carbon\Carbon::parse($demande->date_debut_prevu)->format('d/m/Y') }}<br>
                                au {{ \Carbon\Carbon::parse($demande->date_fin_prevu)->format('d/m/Y') }}
                            </td>
                            <td class="text-start small text-truncate" style="max-width: 180px;" title="{{ $demande->motif }}">{{ $demande->motif ?? '-' }}</td>
                            <td>
                                @switch($demande->statut_demande)
                                    @case('approuvee') <span class="text-success fw-bold">Approuvée</span> @break
                                    @case('rejetee') <span class="text-danger fw-bold">Rejetée</span> @break
                                    @default <span class="text-warning fw-bold font-monospace text-lowercase">{{ $demande->statut_demande }}</span>
                                @endswitch
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted p-3">Aucune demande soumise à ce jour.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mb-4 page-break-inside-prevent">
            <h6 class="text-brown fw-bold border-bottom pb-1 mb-2 text-uppercase small"><i class="fa-solid fa-clock me-2"></i>Plannings Horaires Récurrents</h6>
            <table class="table table-sm table-bordered bg-white text-center small align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Réf Planning</th>
                        <th>Date Début</th>
                        <th>Date Fin</th>
                        <th>Heure Début</th>
                        <th>Heure Fin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employe->planningsJournaliers as $planning)
                        <tr>
                            <td class="font-monospace text-muted">{{ $planning->code_planning }}</td>
                            <td>{{ \Carbon\Carbon::parse($planning->date_debut)->format('d/m/Y') }}</td>
                            <td>{{ $planning->date_fin ? \Carbon\Carbon::parse($planning->date_fin)->format('d/m/Y') : 'Permanent' }}</td>
                            <td class="fw-bold text-primary">{{ substr($planning->heure_debut, 0, 5) }}</td>
                            <td class="fw-bold text-primary">{{ substr($planning->heure_fin, 0, 5) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted p-2">Aucun planning horaire affecté.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mb-4 page-break-inside-prevent">
            <h6 class="text-brown fw-bold border-bottom pb-1 mb-2 text-uppercase small"><i class="fa-solid fa-fingerprint me-2"></i>Journal d'Audit RH & Activités (Sécurité)</h6>
            <table class="table table-sm table-bordered bg-white small align-middle">
                <thead class="table-light text-center">
                    <tr>
                        <th>Code Log</th>
                        <th>Date & Heure</th>
                        <th>Action effectuée</th>
                        <th>Table affectée</th>
                        <th>Adresse IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employe->journalisations->sortByDesc('date_action')->take(8) as $log)
                        <tr>
                            <td class="font-monospace text-muted text-center small">{{ $log->code_log }}</td>
                            <td class="text-center small">{{ \Carbon\Carbon::parse($log->date_action)->format('d/m/Y H:i:s') }}</td>
                            <td><span class="fw-semibold text-dark">{{ $log->action }}</span></td>
                            <td class="text-center text-muted font-monospace small">{{ $log->table_concernee ?? '-' }}</td>
                            <td class="text-center font-monospace small text-muted">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted p-2 text-center">Aucune trace de log disponible pour cet agent.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row mt-5 pt-3 d-none d-print-flex justify-content-between text-center align-items-end">
            <div class="col-4">
                <p class="small text-muted mb-5">Le Service RH (Émetteur)</p>
                <div class="border-top pt-1 text-muted small">Signature & Cachet</div>
            </div>
            <div class="col-4">
                <p class="small text-muted mb-5">Visa de la Direction (SOGETHO-SA)</p>
                <div class="border-top pt-1 text-muted small">Signature & Cachet</div>
            </div>
        </div>

    </div>
</div>

<style>
    .text-brown { color: #4a3b32 !important; }
    
    /* Configuration Écran standard */
    @media screen {
        .print-area {
            background: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e2ded8;
            max-width: 950px;
            margin: 0 auto;
        }
    }

    /* Configuration d'impression Professionnelle Haute Fidélité */
    @media print {
        /* Masquage de la navbar globale du layout et des actions */
        body {
            background-color: #fff !important;
            color: #000 !important;
            font-size: 11px !important;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }
        
        /* Cacher la barre d'outils et le header du site */
        .d-print-none, nav, footer, .no-print-container header, .btn, .badge-marron {
            display: none !important;
        }

        .container-fluid {
            padding: 0 !important;
            margin: 0 !important;
        }

        /* Forcer la zone d'impression en plein écran A4 */
        .print-area {
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* Style des tables pour l'impression jet d'encre/laser */
        .table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        .table-light {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .table th, .table td {
            border: 1px solid #999 !important;
            padding: 4px 6px !important;
        }

        /* Sécurité contre la coupure disgracieuse des tableaux au milieu d'une ligne */
        .page-break-inside-prevent {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* Forcer les couleurs d'arrière-plan Bootstrap lors du rendu PDF / Papier */
        .bg-light {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .alert-success {
            background-color: #d1e7dd !important;
            border-color: #badbcc !important;
            color: #0f5132 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        
        /* Configuration des marges de page A4 standardisé */
        @page {
            size: A4 portrait;
            margin: 1.5cm 1.2cm 1.5cm 1.2cm;
        }
    }
</style>
@endsection