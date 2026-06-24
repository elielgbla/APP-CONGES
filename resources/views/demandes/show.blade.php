@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('demandes.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
        </a>
        <button type="button" onclick="ouvrirFenetreImpression('{{ route('demandes.imprimerUnique', $demande->code_demande) }}')" class="btn btn-sm btn-dark px-3">
            <i class="fa-solid fa-print me-1"></i> Imprimer le Titre de Congé
        </button>
    </div>

    <div class="card shadow-sm border-0 p-5 bg-white mx-auto" style="max-width: 850px; font-family: 'Arial', sans-serif; color: #333; line-height: 1.6;">

        <div class="row align-items-center mb-4">
            <div class="col-6">
                <img src="{{ asset('images/palm_club_logo.png') }}" alt="PALM CLUB HOTEL" style="max-height: 65px; object-fit: contain;">
                <div class="text-success small fw-bold ps-2" style="font-size: 0.75rem; letter-spacing: 1px;">L'hospitalité ivoirienne</div>
            </div>
            <div class="col-6 text-end">
                <h1 class="fw-bold mb-0" style="color: #1e5631; font-size: 1.6rem; letter-spacing: 1px;">SOGETHO-SA</h1>
            </div>
        </div>

        <hr style="border-top: 2px solid #1e5631;" class="mt-0 mb-4">

        <div class="row mb-4">
            <div class="col-7">
                @php
                    // Extraction du poste et service actifs de l'employé à l'origine de la demande
                    $posteActuel = $demande->employe->historiquePostes->where('date_fin', null)->first();
                @endphp
                <table class="table table-borderless table-sm mb-0 small">
                    <tr>
                        <td class="fw-bold text-muted" style="width: 140px;">Nom & Prénoms :</td>
                        <td class="fw-bold text-uppercase">{{ $demande->employe->nom }} {{ $demande->employe->prenom }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Fonction :</td>
                        <td>{{ $posteActuel->poste->libelle_poste ?? 'Non définie' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Matricule :</td>
                        <td class="fw-bold text-danger">{{ $demande->employe->matricule_employe }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Service :</td>
                        <td>{{ $posteActuel->service->libelle_service ?? 'Technique' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Contact :</td>
                        <td>{{ $demande->employe->telephone ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-5 text-end HTML-small">
                <p class="mb-0 fw-bold">Abidjan, le {{ \Carbon\Carbon::parse($demande->date_demande)->translatedFormat('d F Y') }}</p>
                <p class="text-muted mt-4 text-start offset-md-3">/-)<br><span class="fw-bold text-dark">Monsieur le Directeur Général de la SOGETHO-SA</span></p>
            </div>
        </div>

        <div class="mb-4 pt-2">
            <p class="mb-3"><span class="fw-bold text-decoration-underline">OBJET</span> : <span class="fw-bold">Demande de {{ $demande->typePermission->libelle_type_permission ?? 'congé annuel' }}</span></p>

            <p class="mt-4">Monsieur le Directeur Général,</p>
            <p class="text-justify">
                Je viens par la présente, solliciter auprès de votre haute bienveillance l'autorisation d'un congé d'un mois.
            </p>

            <p>
                Je propose, avec votre accord la période allant du
                <span class="fw-bold">{{ \Carbon\Carbon::parse($demande->date_debut_prevu)->format('d/m/Y') }}</span>
                au
                <span class="fw-bold">{{ \Carbon\Carbon::parse($demande->date_fin_prevu)->format('d/m/Y') }} inclus</span>.
            </p>

            <div class="alert alert-light border p-2 my-3 small d-inline-block">
                <i class="fa-solid fa-calendar-days text-success me-1"></i>
                Délai de l'absence planifiée :
                <strong class="text-dark">
                    {{ \Carbon\Carbon::parse($demande->date_debut_prevu)->diffInDays(\Carbon\Carbon::parse($demande->date_fin_prevu)) + 1 }} jour(s)
                </strong>
            </div>

            <p class="text-justify">
                Toutefois, je reste disponible, en cas de nécessité, pour tous travaux durant cette période.
            </p>
            <p class="mb-4">
                Dans l'attente d'une suite favorable, je vous prie d'agréer, Monsieur le Directeur Général, mes salutations distinguées.
            </p>
        </div>

        <div class="mt-5">
            <h6 class="text-uppercase fw-bold small mb-2 text-muted text-center" style="letter-spacing: 1px;">Circuit d'évaluation des visas de l'établissement</h6>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle small mb-0" style="border-color: #ddd;">
                    <thead class="table-light text-secondary">
                        <tr style="font-size: 0.75rem; font-weight: bold;">
                            <th style="width: 25%;">1. CHEF DE SERVICE</th>
                            <th style="width: 25%;">2. RESPONSABLE RH</th>
                            <th style="width: 25%;">3. DIR. EXPLOITATION</th>
                            <th style="width: 25%;">4. DIRECTEUR GENERAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height: 90px;">
                            <td>
                                @if($demande->statut_demande !== 'en_attente_n1' && $demande->statut_demande !== 'rejetee')
                                    <span class="badge bg-success-subtle text-success p-1 border border-success-subtle rounded-pill"><i class="fa-solid fa-circle-check"></i> Accordé</span>
                                @elseif($demande->statut_demande === 'en_attente_n1')
                                    <span class="badge bg-warning-subtle text-warning p-1 border border-warning-subtle rounded-pill"><i class="fa-solid fa-clock"></i> En attente</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>
                                @if(in_array($demande->statut_demande, ['en_attente_n3', 'en_attente_n4', 'approuvee']))
                                    <span class="badge bg-success-subtle text-success p-1 border border-success-subtle rounded-pill"><i class="fa-solid fa-circle-check"></i> Accordé</span>
                                @elseif($demande->statut_demande === 'en_attente_n2')
                                    <span class="badge bg-warning-subtle text-warning p-1 border border-warning-subtle rounded-pill"><i class="fa-solid fa-clock"></i> À traiter</span>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">En attente N1</span>
                                @endif
                            </td>

                            <td>
                                @if(in_array($demande->statut_demande, ['en_attente_n4', 'approuvee']))
                                    <span class="badge bg-success-subtle text-success p-1 border border-success-subtle rounded-pill"><i class="fa-solid fa-circle-check"></i> Accordé</span>
                                @elseif($demande->statut_demande === 'en_attente_n3')
                                    <span class="badge bg-warning-subtle text-warning p-1 border border-warning-subtle rounded-pill"><i class="fa-solid fa-clock"></i> À traiter</span>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">En attente N2</span>
                                @endif
                            </td>

                            <td>
                                @if($demande->statut_demande === 'approuvee')
                                    <span class="badge bg-success p-2 text-white rounded-pill"><i class="fa-solid fa-file-signature"></i> Validé (DG)</span>
                                @elseif($demande->statut_demande === 'en_attente_n4')
                                    <span class="badge bg-warning-subtle text-warning p-1 border border-warning-subtle rounded-pill"><i class="fa-solid fa-clock"></i> Décision DG</span>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">En attente N3</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($demande->statut_demande === 'rejetee')
                <div class="alert alert-danger border-0 shadow-sm mt-3 p-2 text-center small">
                    <i class="fa-solid fa-circle-xmark me-1"></i> <strong>Dossier rejeté lors de l'instruction hiérarchique.</strong>
                </div>
            @endif
        </div>

        {{-- Filtrage strict : Seul l'échelon managérial attendu voit le formulaire (L'ADMIN voit un bandeau informatif) --}}
        @if(Auth::user()->code_poste === 'ADMIN')
            <div class="alert alert-info border-0 shadow-sm mt-5 p-3 text-center no-print">
                <i class="fa-solid fa-eye me-1"></i> Vous consultez ce document avec un compte <strong>Administrateur</strong> (Mode Observation uniquement).
            </div>
        @elseif(
            ($demande->statut_demande === 'en_attente_n1' && in_array(Auth::user()->code_poste, ['RESP_SERVI', 'RESPONSABLE'])) ||
            ($demande->statut_demande === 'en_attente_n2' && Auth::user()->code_poste === 'RH') ||
            ($demande->statut_demande === 'en_attente_n3' && in_array(Auth::user()->code_poste, ['DIR_EXPLOI', 'DIR_EXPLOITATION'])) ||
            ($demande->statut_demande === 'en_attente_n4' && in_array(Auth::user()->code_poste, ['DIR_GENERA', 'DIR_GENERAL']))
        )
            <div class="card bg-light border-success mt-5 p-4 no-print">
                <h5 class="fw-bold text-success mb-3" style="font-size: 1rem;">
                    <i class="fa-solid fa-stamp me-1"></i> Enregistrer votre décision (Traitement de l'Échelon actuel)
                </h5>

                {{-- CORRECTION : Liaison vers la méthode 'valider' du DemandeController --}}
                <form action="{{ route('demandes.valider', $demande->code_demande) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Votre Décision</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="accorder" value="approuver" checked>
                                <label class="form-check-label text-success fw-bold small" for="accorder">Accorder le visa (Suivant)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="rejeter" value="rejeter">
                                <label class="form-check-label text-danger fw-bold small" for="rejeter">Rejeter la demande</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold" for="commentaire">Commentaire / Mention obligatoire</label>
                        <textarea name="commentaire" id="commentaire" rows="2" class="form-control form-control-sm" placeholder="Ex: Avis favorable pour la continuité du service technique..." required></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-sm btn-success px-4 fw-bold">Appliquer mon Visa</button>
                    </div>
                </form>
            </div>
        @endif

    </div>
</div>

<script>
function ouvrirFenetreImpression(url) {
    const largeur = 900;
    const hauteur = 750;
    const gauche = (screen.width - largeur) / 2;
    const haut = (screen.height - hauteur) / 2;

    const popup = window.open(
        url,
        'Impression_Titre_Conge',
        `width=${largeur},height=${hauteur},top=${haut},left=${gauche},resizable=yes,scrollbars=yes,status=no,toolbar=no,menubar=no`
    );

    if (window.focus && popup) {
        popup.focus();
    }
}
</script>

<style>
@media print {
    .no-print, .btn, form, .navbar, footer, .alert {
        display: none !important;
    }
    .card {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection