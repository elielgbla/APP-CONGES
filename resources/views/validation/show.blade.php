@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('demandes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
        </a>

        @if($demande->statut_demande === 'approuvee')
            <a href="{{ route('demandes.imprimerUnique', $demande->code_demande) }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-print me-1"></i> Imprimer le Titre de Congé
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-3">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-3">
            {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0 text-uppercase"><i class="fas fa-folder-open me-2"></i> Dossier #{{ $demande->code_demande }}</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <span class="small d-block text-muted text-uppercase mb-1">Statut du dossier</span>
                        @if($demande->statut_demande === 'en_attente_n1')
                            <span class="badge bg-warning text-dark px-3 py-2 fs-6">Attente Visa Responsable (N1)</span>
                        @elseif($demande->statut_demande === 'en_attente_n2')
                            <span class="badge bg-info text-dark px-3 py-2 fs-6">Attente Visa RH (N2)</span>
                        @elseif($demande->statut_demande === 'en_attente_n3')
                            <span class="badge bg-primary text-white px-3 py-2 fs-6">Attente Visa Directeur Exploitation (N3)</span>
                        @elseif($demande->statut_demande === 'en_attente_n4')
                            <span class="badge bg-secondary text-white px-3 py-2 fs-6">Attente Visa Directeur Général (N4)</span>
                        @elseif($demande->statut_demande === 'approuvee')
                            <span class="badge bg-success px-3 py-2 fs-6">Approuvée / Clôturée</span>
                        @elseif($demande->statut_demande === 'rejetee')
                            <span class="badge bg-danger px-3 py-2 fs-6">Rejetée / Dossier Clos</span>
                        @endif
                    </div>

                    <h5 class="border-bottom pb-2 mb-3 text-secondary small text-uppercase font-weight-bold">Informations sur l'Agent</h5>
                    <table class="table table-sm table-borderless mb-4">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Matricule :</td>
                            <td class="font-weight-bold font-monospace">{{ $demande->employe->matricule_employe }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nom & Prénoms :</td>
                            <td class="font-weight-bold">{{ $demande->employe->nom }} {{ $demande->employe->prenoms }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Service / Direction :</td>
                            <td>{{ $demande->employe->service->libelle_service ?? 'Non assigné' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Poste Occupe :</td>
                            <td>{{ $demande->employe->poste->libelle_poste ?? $demande->employe->code_poste }}</td>
                        </tr>
                    </table>

                    <h5 class="border-bottom pb-2 mb-3 text-secondary small text-uppercase font-weight-bold">Détails de la Absence</h5>
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted" style="width: 40%;">Type de Congé :</td>
                            <td><span class="badge bg-light text-dark border">{{ $demande->typePermission->libelle_type }}</span></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date de Début :</td>
                            <td class="text-success font-weight-bold">
                                <i class="far fa-calendar-check me-1"></i> {{ \Carbon\Carbon::parse($demande->date_debut_prevu)->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date de Fin :</td>
                            <td class="text-danger font-weight-bold">
                                <i class="far fa-calendar-times me-1"></i> {{ \Carbon\Carbon::parse($demande->date_fin_prevu)->format('d/m/Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Date Demande :</td>
                            <td>{{ \Carbon\Carbon::parse($demande->date_demande)->format('d/m/Y à H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Motif de l'agent :</td>
                            <td class="text-muted font-italic">{{ $demande->motif_demande ?? 'Aucun motif écrit renseigné.' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-4">
            {{-- BLOC 1 : TABLEAU VISUEL DES SIGNATURES --}}
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 text-uppercase text-secondary font-weight-bold"><i class="fas fa-stamp me-2"></i> Circuit de Visa SOGETHO</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center text-uppercase">
                        <div class="col-md-6 border-end py-2">
                            <h6 class="text-muted small mb-1">1. RECOMMANDATION RESPONSABLE</h6>
                            <hr class="my-2">
                            @if(isset($historiqueValidations[1]))
                                <div class="p-2">
                                    <span class="badge bg-{{ $historiqueValidations[1]->decision === 'valide' ? 'success' : 'danger' }} mb-2">
                                        {{ $historiqueValidations[1]->decision === 'valide' ? 'ACCORDÉ' : 'REJETÉ' }}
                                    </span>
                                    <p class="mb-1 text-muted" style="font-size: 0.8rem;">Le {{ \Carbon\Carbon::parse($historiqueValidations[1]->date_decision)->format('d/m/Y') }}</p>
                                    <p class="mb-2 text-wrap text-muted text-lowercase font-italic small" style="max-height: 50px; overflow: hidden;">
                                        " {{ $historiqueValidations[1]->commentaire }} "
                                    </p>

                                    @if($historiqueValidations[1]->signature)
                                        <img src="{{ asset('storage/' . $historiqueValidations[1]->signature) }}" alt="Signature N1" class="img-fluid" style="max-height: 55px; object-fit: contain;">
                                    @else
                                        <p class="text-dark font-italic small font-weight-bold mt-2 mb-0">{{ $historiqueValidations[1]->valideur->nom }}</p>
                                    @endif
                                </div>
                            @else
                                <div class="py-4 text-muted small font-italic">En attente de traitement</div>
                            @endif
                        </div>

                        <div class="col-md-6 py-2">
                            <h6 class="text-muted small mb-1">2. DÉCISION DIRECTION RH</h6>
                            <hr class="my-2">
                            @if(isset($historiqueValidations[2]))
                                <div class="p-2">
                                    <span class="badge bg-{{ $historiqueValidations[2]->decision === 'valide' ? 'success' : 'danger' }} mb-2">
                                        {{ $historiqueValidations[2]->decision === 'valide' ? 'APPROUVÉ' : 'REJETÉ' }}
                                    </span>
                                    <p class="mb-1 text-muted" style="font-size: 0.8rem;">Le {{ \Carbon\Carbon::parse($historiqueValidations[2]->date_decision)->format('d/m/Y') }}</p>
                                    <p class="mb-2 text-wrap text-muted text-lowercase font-italic small" style="max-height: 50px; overflow: hidden;">
                                        " {{ $historiqueValidations[2]->commentaire }} "
                                    </p>

                                    @if($historiqueValidations[2]->signature)
                                        <img src="{{ asset('storage/' . $historiqueValidations[2]->signature) }}" alt="Signature N2" class="img-fluid" style="max-height: 55px; object-fit: contain;">
                                    @else
                                        <p class="text-dark font-italic small font-weight-bold mt-2 mb-0">{{ $historiqueValidations[2]->valideur->nom }}</p>
                                    @endif
                                </div>
                            @else
                                <div class="py-4 text-muted small font-italic">En attente de traitement</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- BLOC 2 : FORMULAIRE INTERACTIF DE VALIDATION --}}
            @php
                $user = Auth::user();
                $afficherFormulaire = false;
                $niveauValidationRequis = 0;

                // Calcul dynamique des droits pour l'affichage du formulaire
                if ($demande->statut_demande === 'en_attente_n1' && in_array($user->code_poste, ['RESP_SERVI', 'RESPONSABLE']) && $user->code_service === $demande->employe->code_service) {
                    $afficherFormulaire = true;
                    $niveauValidationRequis = 1;
                } elseif ($demande->statut_demande === 'en_attente_n2' && $user->code_poste === 'RH') {
                    $afficherFormulaire = true;
                    $niveauValidationRequis = 2;
                } elseif ($demande->statut_demande === 'en_attente_n3' && in_array($user->code_poste, ['DIR_EXPLOI', 'DIR_EXPLOITATION'])) {
                    $afficherFormulaire = true;
                    $niveauValidationRequis = 3;
                } elseif ($demande->statut_demande === 'en_attente_n4' && in_array($user->code_poste, ['DIR_GENERA', 'DIR_GENERAL'])) {
                    $afficherFormulaire = true;
                    $niveauValidationRequis = 4;
                }
            @endphp

            @if($afficherFormulaire)
                <div class="card shadow-sm border-0 bg-light animate__animated animate__fadeIn">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0"><i class="fas fa-pen-nib me-2"></i> Poser votre Visa Décisionnel (Niveau {{ $niveauValidationRequis }})</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('validations.store', $demande->code_demande) }}" method="POST">
                            @csrf
                            <input type="hidden" name="niveau_validation" value="{{ $niveauValidationRequis }}">

                            <div class="mb-3">
                                <label class="form-label d-block font-weight-bold text-dark">Votre décision :</label>
                                <div class="form-check form-check-inline me-4">
                                    <input class="form-check-input" type="radio" name="decision" id="approuver" value="valide" required>
                                    <label class="form-check-label text-success font-weight-bold style-pointer" for="approuver">
                                        <i class="fas fa-check-circle me-1"></i> Accorder le congé
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="decision" id="rejeter" value="rejete" required>
                                    <label class="form-check-label text-danger font-weight-bold style-pointer" for="rejeter">
                                        <i class="fas fa-times-circle me-1"></i> Rejeter le dossier
                                    </label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="commentaire" class="form-label font-weight-bold text-dark">Observations / Motifs du visa :</label>
                                <textarea class="form-control @error('commentaire') is-invalid @enderror" id="commentaire" name="commentaire" rows="3" placeholder="Obligatoire. Saisissez les mentions ou motifs justifiant votre décision..." required>{{ old('commentaire') }}</textarea>
                                @error('commentaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-success px-4 shadow-sm">
                                    <i class="fas fa-check me-2"></i> Valider et Enregistrer l'Étape
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-secondary border-0 shadow-sm text-center py-3">
                    <i class="fas fa-info-circle fa-lg mb-2 text-secondary"></i><br>
                    <span class="small text-muted">
                        Vous n'avez aucune action en attente à effectuer sur ce dossier à ce stade du circuit.
                    </span>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
