@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="background-color: #fcfbfa;">

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: #e8e5e1 !important;">
        <div>
            <a href="{{ route('journalisation.index') }}" class="btn btn-sm btn-outline-secondary mb-2" style="color: #4a3b32; border-color: #dcd7ce;">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
            <h1 class="h3 mb-1" style="color: #4a3b32; font-weight: 700;">
                <i class="fas fa-search me-2" style="color: #2c5e3b;"></i>Détails de la Trace d'Audit
            </h1>
            <p class="text-muted mb-0">Identifiant unique du log : <strong class="text-monospace">{{ $log->code_log }}</strong></p>
        </div>

        <span class="badge px-3 py-2 text-white" style="background-color: #4a3b32;">
            <i class="fas fa-shield-alt me-1"></i> Document Non Modifiable
        </span>
    </div>

    <div class="row g-4">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header py-3 text-white" style="background-color: #4a3b32;">
                    <h5 class="card-title mb-0 small text-uppercase fw-bold tracking-wide">Données enregistrées</h5>
                </div>
                <div class="card-body bg-white p-4">
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold small text-uppercase text-muted">Explication claire de l'opération</label>
                        <div class="p-3 rounded border text-dark" style="font-size: 1.05rem; line-height: 1.6; background-color: #faf9f6; border-color: #efece6 !important;">
                            @php $jsonData = json_decode($log->details, true); @endphp

                            @if(is_array($jsonData))
                                <p class="mb-2">
                                    Le collaborateur a sollicité le serveur en ouvrant la page 
                                    <strong class="text-success">{{ $jsonData['url'] ?? 'non spécifiée' }}</strong>.
                                </p>
                                <p class="mb-2">
                                    Cette demande a été acheminée à l'aide de la méthode d'envoi <strong>{{ $jsonData['method'] ?? 'GET' }}</strong>.
                                </p>
                                @if(!empty($jsonData['input']))
                                    <div class="mt-3">
                                        <span class="d-block small text-muted fw-bold text-uppercase mb-1">Champs ou paramètres saisis :</span>
                                        <pre class="bg-dark text-light p-3 rounded font-monospace small mb-0" style="max-height: 200px; overflow-y: auto;">{{ json_encode($jsonData['input'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                @else
                                    <span class="text-muted small"><i class="fas fa-info-circle me-1"></i> Aucune donnée de formulaire ou paramètre sensible n'a été transféré lors de cet appel.</span>
                                @endif
                            @else
                                {{ $log->details }}
                            @endif
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Action système détectée</label>
                            <p class="fs-5 fw-semibold" style="color: #2c5e3b;">
                                <i class="fas fa-cog me-1 opacity-75"></i> {{ $log->action }}
                            </p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Table SQL Impactée</label>
                            <p class="fs-5 text-uppercase font-monospace fw-bold" style="color: #6e584a;">
                                <i class="fas fa-database me-1 opacity-75"></i> 
                                {{ $log->table_concernee ?? $log->table_cible ?? 'APPLICATION GLOBALE' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden; border-top: 4px solid #2c5e3b !important;">
                <div class="card-body p-4 bg-white">
                    <h5 class="fw-bold mb-4" style="color: #4a3b32;">Informations Contexte</h5>

                    <div class="d-flex align-items-start mb-3">
                        <div class="bg-light p-2 rounded text-muted me-3">
                            <i class="fas fa-user-shield fs-4"></i>
                        </div>
                        <div>
                            <span class="d-block text-uppercase small text-muted fw-bold">Auteur de l'action</span>
                            @if($log->employe)
                                <span class="d-block fw-bold text-dark mt-1" style="font-size: 1.05rem;">
                                    {{ $log->employe->nom }} {{ $log->employe->prenom }}
                                </span>
                            @endif
                            <span class="badge mt-1 px-2 py-1" style="background-color: #efece6; color: #5a4a42; font-size: 0.85rem;">
                                Matricule : {{ $log->matricule_employe ?? 'SYSTEM' }}
                            </span>
                        </div>
                    </div>

                    <hr style="border-color: #f2eee9;">

                    <div class="d-flex align-items-start mb-3">
                        <div class="bg-light p-2 rounded text-muted me-3">
                            <i class="fas fa-clock fs-4"></i>
                        </div>
                        <div>
                            <span class="d-block text-uppercase small text-muted fw-bold">Date et Heure</span>
                            <span class="fw-bold d-block mt-1" style="color: #4a3b32;">
                                {{ $log->date_action ? \Carbon\Carbon::parse($log->date_action)->format('d/m/Y à H:i:s') : 'Non renseignée' }}
                            </span>
                        </div>
                    </div>

                    <hr style="border-color: #f2eee9;">

                    <div class="d-flex align-items-start">
                        <div class="bg-light p-2 rounded text-muted me-3">
                            <i class="fas fa-network-wired fs-4"></i>
                        </div>
                        <div>
                            <span class="d-block text-uppercase small text-muted fw-bold">Adresse IP Réseau</span>
                            <code class="d-block mt-1 fs-6 text-dark fw-semibold">
                                {{ $log->ip_address ?? '127.0.0.1' }}
                            </code>
                        </div>
                    </div>
                </div>

                <div class="card-footer p-3 border-0 text-center small text-muted" style="background-color: #f4f1ea;">
                    <i class="fas fa-info-circle me-1 text-success"></i> Généré automatiquement par l'application.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection