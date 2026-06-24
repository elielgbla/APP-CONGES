@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="row">
    <div class="col-12">
        
        {{-- En-tête du Tableau de Bord --}}
        <div class="d-flex justify-content-between align-items-center pb-2 mb-4 border-bottom">
            <div>
                <h1 class="h2 fw-bold text-brown mb-1">Tableau de bord</h1>
                <p class="text-muted mb-0">Ravi de vous revoir, <strong>{{ $user->prenom }} {{ $user->nom }}</strong></p>
            </div>
            <span class="badge bg-dark p-2 shadow-sm rounded-pill px-3">
                <i class="fa-solid fa-calendar-day me-2 text-success"></i>{{ now()->format('d/m/Y') }}
            </span>
        </div>

        {{-- Section des Cartes de Statistiques --}}
        <div class="row g-3 mb-4">
            
            {{-- 1. Solde de congés (Tous les utilisateurs) --}}
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white h-100">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1 fw-semibold">Solde de congés</h6>
                            <h3 class="fw-bold mb-0 text-brown">{{ $stats['conge_restant'] }} Jours</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success">
                            <i class="fa-solid fa-umbrella-beach fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Mes demandes (Tous les utilisateurs) --}}
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white h-100" style="border-left-color: #0d6efd;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1 fw-semibold">Mes demandes</h6>
                            <h3 class="fw-bold mb-0 text-primary">{{ $stats['mes_demandes_compte'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary">
                            <i class="fa-solid fa-folder-open fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. À valider (Visible uniquement par les managers et le circuit de visa) --}}
            @if(in_array($user->code_poste, ['RESP_SERVI', 'RH', 'DIR_EXPLOI', 'DIR_GENERA']) || $user->code_poste === 'ADMIN')
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white h-100" style="border-left-color: #ff9800;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1 fw-semibold">À valider</h6>
                            <h3 class="fw-bold mb-0 text-warning">
                                {{ $stats['en_attente_validation'] }}
                            </h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning">
                            <i class="fa-solid fa-stamp fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 4. Effectif Total (Visible uniquement par RH et ADMIN) --}}
            @if(in_array($user->code_poste, ['RH', 'ADMIN']))
            <div class="col-12 col-sm-6 col-lg-3">
                <div class="card card-stat p-3 bg-white h-100" style="border-left-color: #5d4037;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-muted text-uppercase small mb-1 fw-semibold">Effectif Total</h6>
                            <h3 class="fw-bold mb-0 text-brown">{{ $stats['total_employes'] }}</h3>
                        </div>
                        <div class="bg-dark bg-opacity-10 p-3 rounded-3 text-dark">
                            <i class="fa-solid fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>

        {{-- NOUVEAU AJOUT : Actions Rapides Contextuelles --}}
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h5 class="fw-bold text-brown mb-3"><i class="fa-solid fa-bolt me-2 text-warning"></i> Actions Rapides</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('demandes.index') }}" class="btn btn-outline-success fw-semibold">
                            <i class="fa-solid fa-plus me-2"></i>Nouvelle demande de congé
                        </a>
                        
                        @if(in_array($user->code_poste, ['RESP_SERVI', 'RH', 'DIR_EXPLOI', 'DIR_GENERA']))
                            <a href="{{ route('validations.index') }}" class="btn btn-outline-warning fw-semibold">
                                <i class="fa-solid fa-check-double me-2"></i>Traiter les validations
                            </a>
                        @endif

                        @if(in_array($user->code_poste, ['RH', 'ADMIN']))
                            <a href="{{ route('employes.index') }}" class="btn btn-outline-primary fw-semibold">
                                <i class="fa-solid fa-users me-2"></i>Gérer le personnel
                            </a>
                            <a href="{{ route('plannings.index') }}" class="btn btn-outline-secondary fw-semibold">
                                <i class="fa-solid fa-calendar-days me-2"></i>Plannings Horaires
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Section Activités Récentes (Structure Professionnelle sous forme de Timeline) --}}
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm p-4 bg-white">
                    <h5 class="fw-bold text-brown mb-3"><i class="fa-solid fa-clock-history me-2"></i> Activités Récentes</h5>
                    
                    {{-- Timeline stylisée pour le suivi --}}
                    <div class="position-relative ps-3 border-left style-timeline mt-3">
                        <div class="mb-3 position-relative">
                            <i class="fa-solid fa-circle text-primary position-absolute start-0 translate-middle bg-white" style="margin-left: -16px; margin-top: 5px; font-size: 12px;"></i>
                            <div class="ms-3">
                                <span class="small text-muted d-block">Aujourd'hui à {{ now()->format('H:i') }}</span>
                                <p class="mb-0 text-dark">Connexion sécurisée réussie sur le portail RH de SOGETHO-SA depuis votre poste informatique.</p>
                            </div>
                        </div>

                        {{-- Section prête pour une boucle @foreach ultérieure pour tes demandes --}}
                        <div class="mb-2 position-relative">
                            <i class="fa-solid fa-circle text-muted position-absolute start-0 translate-middle bg-white" style="margin-left: -16px; margin-top: 5px; font-size: 12px;"></i>
                            <div class="ms-3">
                                <span class="small text-muted d-block">Historique global</span>
                                <p class="text-muted mb-0 small">Le suivi complet de vos dépôts de congés et de vos validations de circuits de visas s'affichera de manière dynamique ici.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection