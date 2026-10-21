@extends('layouts.app')

@section('content')
@php
    // Extraction du poste et service actifs de l'employé à l'origine de la demande
    $posteActuelEmploye = $demande->employe->historiquePostes->where('date_fin', null)->first();
    
    // Évaluation dynamique des droits de validation pour l'utilisateur connecté
    $posteValideur = strtoupper(Auth::user()->code_poste);
    $peutValider = false;
    $niveauEnCours = null;

    if ($demande->matricule_employe !== Auth::user()->matricule_employe && $demande->statut_demande === 'en_attente_n1' && in_array($posteValideur, ['RESP_SERVI', 'RESPONSABLE'])) {
        $peutValider = true;
        $niveauEnCours = 1;
    } elseif ($demande->matricule_employe !== Auth::user()->matricule_employe && $demande->statut_demande === 'en_attente_n2' && $posteValideur === 'RH') {
        $peutValider = true;
        $niveauEnCours = 2;
    } elseif ($demande->matricule_employe !== Auth::user()->matricule_employe && $demande->statut_demande === 'en_attente_n3' && in_array($posteValideur, ['DIR_EXPLOI', 'DIR_EXPLOITATION'])) {
        $peutValider = true;
        $niveauEnCours = 3;
    } elseif ($demande->matricule_employe !== Auth::user()->matricule_employe && $demande->statut_demande === 'en_attente_n4' && in_array($posteValideur, ['DIR_GENERA', 'DIR_GENERAL'])) {
        $peutValider = true;
        $niveauEnCours = 4;
    }
@endphp

<div class="container-fluid pb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4 no-print border-bottom pb-3" style="border-color: #e8e5e1 !important;">
        <div>
            <a href="{{ route('demandes.index') }}" class="text-decoration-none text-muted mb-2 d-inline-block small">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour au registre
            </a>
            <h1 class="h4 fw-bold text-brown mb-0" style="color: #4a3b32;">
                Dossier de Demande <span class="text-secondary font-monospace ms-2">#{{ $demande->code_demande }}</span>
            </h1>
        </div>
        <div class="d-flex gap-2">
            <button type="button" onclick="ouvrirFenetreImpression('{{ route('demandes.imprimerUnique', $demande->code_demande) }}')" class="btn btn-sm text-white px-3" style="background-color: #4a3b32; font-weight: 500; border-radius: 6px;">
                <i class="fa-solid fa-print me-1"></i> Imprimer le Titre
            </button>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4 no-print" style="border-radius: 8px; background-color: #e8f5e9; color: #1b5e20;">
        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm mb-4 no-print" style="border-radius: 8px; background-color: #ffebee; color: #c62828;">
        <i class="fa-solid fa-circle-xmark me-2"></i> {{ session('error') }}
    </div>
    @endif
    
    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm mb-4 no-print" style="border-radius: 8px;">
        <p class="fw-bold mb-1"><i class="fa-solid fa-triangle-exclamation me-1"></i> La validation a échoué :</p>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow-sm border-0 p-5 bg-white mx-auto printable-card" style="max-width: 850px; border-radius: 12px; font-family: 'Arial', sans-serif; color: #333; line-height: 1.6;">

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
                <table class="table table-borderless table-sm mb-0 small">
                    <tr>
                        <td class="fw-bold text-muted" style="width: 140px;">Nom & Prénoms :</td>
                        <td class="fw-bold text-uppercase">{{ $demande->employe->nom ?? 'Inconnu' }} {{ $demande->employe->prenom ?? '' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Fonction :</td>
                        <td>{{ $posteActuelEmploye->poste->libelle_poste ?? 'Non définie' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Matricule :</td>
                        <td class="fw-bold text-danger">{{ $demande->matricule_employe }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Service :</td>
                        <td>{{ $posteActuelEmploye->service->libelle_service ?? 'Technique' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-muted">Contact :</td>
                        <td>{{ $demande->employe->telephone ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-5 text-end small">
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

            <div class="alert alert-light border p-2 my-3 small d-inline-block" style="background-color: #fcfbf9 !important; border-color: #e8e5e1 !important;">
                <i class="fa-solid fa-calendar-days text-success me-1"></i> 
                Délai de l'absence planifiée : 
                <strong class="text-dark">
                    {{ $demande->nombre_jours ?? (\Carbon\Carbon::parse($demande->date_debut_prevu)->diffInDays(\Carbon\Carbon::parse($demande->date_fin_prevu)) + 1) }} jour(s)
                </strong>
            </div>

            <p class="text-justify">
                Toutefois, je reste disponible, en cas de nécessité, pour tous travaux durant cette période.
            </p>
            <p class="mb-4">
                Dans l'attente d'une suite favorable, je vous prie d'agréer, Monsieur le Directeur Général, mes salutations distinguées.
            </p>
        </div>

        <!-- STREAMING_CHUNK: Tableau de suivi des visas (Mis à jour N1->N4) -->
        <div class="mt-5">
            <h6 class="text-uppercase fw-bold small mb-2 text-muted text-center" style="letter-spacing: 1px;">Circuit d'évaluation des visas de l'établissement</h6>
            <div class="table-responsive">
                <table class="table table-bordered text-center align-middle small mb-0" style="border-color: #ddd;">
                    <thead class="table-light text-secondary">
                        <tr style="font-size: 0.75rem; font-weight: bold;">
                            <th style="width: 25%;">1. CHEF DE SERVICE</th>
                            <th style="width: 25%;">2. RESSOURCES HUMAINES</th>
                            <th style="width: 25%;">3. DIR. EXPLOITATION</th>
                            <th style="width: 25%;">4. DIRECTEUR GENERAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr style="height: 90px;">
                            <!-- N1: Chef de Service -->
                            <td>
                                @if($demande->statut_demande !== 'en_attente_n1' && $demande->statut_demande !== 'rejetee')
                                    <span class="badge bg-success bg-opacity-10 text-success p-2 border border-success border-opacity-25 rounded-pill"><i class="fa-solid fa-circle-check"></i> Accordé</span>
                                @elseif($demande->statut_demande === 'en_attente_n1')
                                    <span class="badge bg-warning bg-opacity-10 text-warning p-2 border border-warning border-opacity-25 rounded-pill" style="color: #b58105 !important;"><i class="fa-solid fa-clock"></i> En attente</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <!-- N2: Ressources Humaines -->
                            <td>
                                @if(in_array($demande->statut_demande, ['en_attente_n3', 'en_attente_n4', 'approuvee']))
                                    <span class="badge bg-success bg-opacity-10 text-success p-2 border border-success border-opacity-25 rounded-pill"><i class="fa-solid fa-circle-check"></i> Accordé</span>
                                @elseif($demande->statut_demande === 'en_attente_n2')
                                    <span class="badge bg-warning bg-opacity-10 text-warning p-2 border border-warning border-opacity-25 rounded-pill" style="color: #b58105 !important;"><i class="fa-solid fa-clock"></i> À traiter</span>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">En attente N1</span>
                                @endif
                            </td>

                            <!-- N3: Dir. Exploitation -->
                            <td>
                                @if(in_array($demande->statut_demande, ['en_attente_n4', 'approuvee']))
                                    <span class="badge bg-success bg-opacity-10 text-success p-2 border border-success border-opacity-25 rounded-pill"><i class="fa-solid fa-circle-check"></i> Accordé</span>
                                @elseif($demande->statut_demande === 'en_attente_n3')
                                    <span class="badge bg-warning bg-opacity-10 text-warning p-2 border border-warning border-opacity-25 rounded-pill" style="color: #b58105 !important;"><i class="fa-solid fa-clock"></i> Décision DG</span>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">En attente N2</span>
                                @endif
                            </td>

                            <!-- N4: Directeur Général -->
                            <td>
                                @if($demande->statut_demande === 'approuvee')
                                    <span class="badge bg-success p-2 text-white rounded-pill"><i class="fa-solid fa-file-signature"></i> Validé & Planifié</span>
                                @elseif($demande->statut_demande === 'en_attente_n4')
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary p-2 border border-secondary border-opacity-25 rounded-pill"><i class="fa-solid fa-clock"></i> Décision DG</span>
                                @else
                                    <span class="text-muted" style="font-size: 0.8rem;">En attente N3</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($demande->statut_demande === 'rejetee')
                <div class="alert alert-danger border-0 shadow-sm mt-3 p-3 text-center small" style="background-color: #ffebee; border-radius: 8px;">
                    <i class="fa-solid fa-circle-xmark me-1"></i> <strong>Dossier rejeté lors de l'instruction hiérarchique.</strong>
                    @if($demande->commentaire_validateur)
                        <div class="mt-2 text-muted fst-italic">"{{ $demande->commentaire_validateur }}"</div>
                    @endif
                </div>
            @endif
        </div>

        @if(Auth::user()->code_poste === 'ADMIN')
            <div class="alert alert-info border-0 shadow-sm mt-5 p-3 text-center no-print" style="border-radius: 8px;">
                <i class="fa-solid fa-eye me-1"></i> Vous consultez ce document avec un compte <strong>Administrateur</strong> (Mode Audit uniquement).
            </div>
       @elseif($peutValider)
            <div class="card bg-light border-0 mt-5 p-4 no-print shadow-sm" style="border-radius: 12px; border-left: 4px solid #2e7d32 !important; background-color: #f8f9fa !important;">
                <h5 class="fw-bold text-dark mb-3 d-flex align-items-center" style="font-size: 1.1rem;">
                    <span class="bg-success text-white rounded-circle d-inline-flex justify-content-center align-items-center me-2" style="width: 28px; height: 28px;"><i class="fa-solid fa-pen-nib small"></i></span>
                    Enregistrer votre décision (Niveau {{ $niveauEnCours }})
                </h5>

                <form action="{{ route('validations.store') }}" method="POST" onsubmit="let btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = 'Traitement en cours... <i class=\'fa-solid fa-spinner fa-spin ms-2\'></i>';">
                    @csrf
                    <!-- Le champ caché est vital pour le Controller -->
                    <input type="hidden" name="code_demande" value="{{ $demande->code_demande }}">
                    <input type="hidden" name="niveau_validation" value="{{ $niveauEnCours }}">

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-dark">Votre Décision</label>
                        <div class="d-flex gap-4 p-3 bg-white border rounded" style="border-color: #e8e5e1 !important;">
                            <div class="form-check form-check-inline mb-0">
                                <!-- CORRECTION MAJEURE: name="decision" (au lieu de action) -->
                                <input class="form-check-input" type="radio" name="decision" id="accorder" value="approuver" checked>
                                <label class="form-check-label text-success fw-bold" for="accorder"><i class="fa-solid fa-check me-1"></i> Accorder le visa (Suivant)</label>
                            </div>
                            <div class="form-check form-check-inline mb-0">
                                <input class="form-check-input" type="radio" name="decision" id="rejeter" value="rejeter">
                                <label class="form-check-label text-danger fw-bold" for="rejeter"><i class="fa-solid fa-xmark me-1"></i> Rejeter la demande</label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark" for="commentaire">Commentaire (Obligatoire en cas de rejet)</label>
                        <textarea name="commentaire" id="commentaire" rows="2" class="form-control" placeholder="Saisissez vos observations ici..." style="border-color: #e8e5e1;"></textarea>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-success px-5 py-2 fw-bold" style="border-radius: 8px;">
                            Enregistrer la décision <i class="fa-solid fa-paper-plane ms-2"></i>
                        </button>
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
    .printable-card {
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
    }
    body {
        background-color: white !important;
    }
}
</style>
@endsection