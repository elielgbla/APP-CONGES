@extends('layouts.app')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('plannings.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Retour à la liste
        </a>
        <div>
            <button onclick="window.print();" class="btn btn-sm btn-dark px-3 me-2">
                <i class="fa-solid fa-print me-1"></i> Imprimer la fiche
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 bg-white">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-2 text-center mb-3 mb-md-0">
                    <div class="bg-light text-danger rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm border" style="width: 80px; height: 80px;">
                        <i class="fa-solid fa-user-tie fs-2"></i>
                    </div>
                </div>
                <div class="col-md-6 border-end-md">
                    <span class="badge bg-danger mb-2 px-3 py-1 text-uppercase tracking-wider shadow-sm" style="font-size: 0.75rem;">
                        Matricule : {{ $employe->matricule_employe }}
                    </span>
                    <h2 class="h4 fw-bold text-dark mb-1 text-uppercase">{{ $employe->nom }} {{ $employe->prenom }}</h2>
                    <p class="text-muted mb-0 small">
                        <i class="fa-solid fa-briefcase me-1"></i> {{ $employe->poste->libelle_poste ?? 'Poste non défini' }}
                    </p>
                </div>
                <div class="col-md-4 ps-md-4 mt-3 mt-md-0">
                    <p class="mb-1 text-secondary small">
                        <strong>Structure d'affectation :</strong>
                    </p>
                    <div class="p-2 bg-light rounded border">
                        <span class="d-block text-dark fw-bold small text-uppercase">
                            <i class="fa-solid fa-building me-1 text-muted"></i> {{ $employe->service->libelle_service ?? 'Service non spécifié' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm small py-2 mb-3 no-print">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-secondary text-uppercase fw-bold mb-0" style="font-size: 0.75rem; letter-spacing: 0.5px;">
            Grille des Horaires & Périodes de Service
        </h6>
        <a href="{{ route('plannings.create', ['matricule' => $employe->matricule_employe]) }}" class="btn btn-xs btn-outline-success px-2 py-1 no-print" style="font-size: 0.75rem;">
            <i class="fa-solid fa-plus me-1"></i> Ajouter un créneau
        </a>
    </div>

    <div class="card shadow-sm border-0 bg-whitemb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0 small">
                <thead class="table-light text-muted" style="font-size: 0.75rem;">
                    <tr>
                        <th style="width: 12%;">Réf. Planning</th>
                        <th style="width: 18%;">Date de Début</th>
                        <th style="width: 18%;">Date de Fin</th>
                        <th style="width: 14%;">Heure Début</th>
                        <th style="width: 14%;">Heure Fin</th>
                        <th style="width: 24%;" class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programmes as $prog)
                        @php
                            $debut = \Carbon\Carbon::parse($prog->date_debut);
                            $fin = $prog->date_fin ? \Carbon\Carbon::parse($prog->date_fin) : null;
                            $aujourdhui = \Carbon\Carbon::today();
                            
                            // Un planning est actif s'il n'a pas de fin OU si aujourd'hui est situé après le début et avant la fin
                            $estEnCours = !$fin || $aujourdhui->between($debut, $fin);
                        @endphp
                        <tr class="{{ $estEnCours ? 'table-primary table-opacity-10' : '' }}">
                            <td class="text-muted fw-bold text-uppercase" style="font-size: 0.75rem;">
                                {{ $prog->code_planning }}
                                @if($estEnCours)
                                    <span class="d-block text-primary" style="font-size: 0.65rem;">(Actif)</span>
                                @endif
                            </td>
                            <td>
                                <i class="fa-regular fa-calendar text-muted me-1"></i>
                                {{ $debut->format('d/m/Y') }}
                            </td>
                            <td>
                                <i class="fa-regular fa-calendar text-muted me-1"></i>
                                {!! $fin ? $fin->format('d/m/Y') : '<span class="text-secondary italic small">En cours</span>' !!}
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border px-2 py-1">
                                    <i class="fa-regular fa-clock me-1"></i>{{ substr($prog->heure_debut, 0, 5) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-success border px-2 py-1">
                                    <i class="fa-regular fa-clock me-1"></i>{{ substr($prog->heure_fin, 0, 5) }}
                                </span>
                            </td>
                            <td class="no-print">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-dark py-0 px-2" title="Voir le calendrier et statistiques" data-bs-toggle="modal" data-bs-target="#modalPlanning{{ $prog->code_planning }}">
                                        <i class="fa-solid fa-calendar-days text-warning"></i>
                                    </button>

                                    <a href="{{ route('plannings.edit', $prog->code_planning) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Modifier">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>

                                    <form action="{{ route('plannings.destroy', $prog->code_planning) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment retirer ce créneau horaire de la fiche de l\'agent ?');" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Retirer">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-muted py-4">
                                <i class="fa-solid fa-calendar-xmark d-block fs-4 mb-2 text-secondary"></i>
                                Aucun planning n'est actuellement configuré pour cet employé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@foreach($programmes as $prog)
    <div class="modal fade no-print" id="modalPlanning{{ $prog->code_planning }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-dark text-white border-0 py-3">
                    <h5 class="modal-title fw-bold" style="font-size: 1rem;">
                        <i class="fa-solid fa-calendar-days me-2 text-warning"></i>Calendrier Mensuel — Réf #{{ $prog->code_planning }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body p-4 bg-light">
                    <div class="alert bg-white border small text-dark mb-3 p-3 shadow-sm rounded">
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Agent :</strong> {{ $employe->nom }} {{ $employe->prenom }}<br>
                                <strong>Horaires :</strong> {{ substr($prog->heure_debut, 0, 5) }} - {{ substr($prog->heure_fin, 0, 5) }}
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <strong>Début :</strong> {{ \Carbon\Carbon::parse($prog->date_debut)->format('d/m/Y') }}<br>
                                <strong>Fin :</strong> {{ $prog->date_fin ? \Carbon\Carbon::parse($prog->date_fin)->format('d/m/Y') : 'En cours' }}
                            </div>
                        </div>
                    </div>

                    <div class="row g-2 mb-3 text-center">
                        <div class="col-4"><div class="p-2 bg-white rounded border shadow-sm"><span class="d-block text-muted small" style="font-size:0.65rem;">Jours de Service</span><b class="text-success">{{ $prog->total_jours_travail }} j</b></div></div>
                        <div class="col-4"><div class="p-2 bg-white rounded border shadow-sm"><span class="d-block text-muted small" style="font-size:0.65rem;">Jours de Repos</span><b class="text-danger">{{ $prog->total_jours_repos }} j</b></div></div>
                        <div class="col-4"><div class="p-2 bg-white rounded border shadow-sm"><span class="d-block text-muted small" style="font-size:0.65rem;">Heures Totales</span><b class="text-dark">{{ number_format($prog->total_heures_travail, 0, ',', ' ') }} h</b></div></div>
                    </div>

                    <div class="card shadow-sm border-0 bg-white p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle text-center mb-0" style="font-size: 0.75rem;">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width: 14.28%;">Lun</th>
                                        <th style="width: 14.28%;">Mar</th>
                                        <th style="width: 14.28%;">Mer</th>
                                        <th style="width: 14.28%;">Jeu</th>
                                        <th style="width: 14.28%;">Ven</th>
                                        <th style="width: 14.28%;">Sam</th>
                                        <th style="width: 14.28%;">Dim</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
    $dateDebut = \Carbon\Carbon::parse($prog->date_debut);
    
    // Si pas de date de fin, on est toujours actif -> borne à aujourd'hui
    // Si une date de fin existe, on prend la date de fin
    $dateFin = $prog->date_fin ? \Carbon\Carbon::parse($prog->date_fin) : \Carbon\Carbon::today();
    
    $aujourdhui = \Carbon\Carbon::today();
    if ($aujourdhui->between($dateDebut, $dateFin)) {
        $dateFin = $aujourdhui;
    }

    // SÉCURITÉ ANTI-RAM : Si la période dépasse 35 jours (ex: planning clôturé loin dans le futur)
    // On bride l'affichage visuel du calendrier au mois de démarrage pour éviter de saturer la page
    if ($dateDebut->diffInDays($dateFin) > 35) {
        $dateFin = $dateDebut->copy()->endOfMonth();
    }

    $startOfCalendar = $dateDebut->copy()->startOfWeek(); 
    $endOfCalendar = $dateFin->copy()->endOfWeek();
    $period = \Carbon\CarbonPeriod::create($startOfCalendar, $endOfCalendar);
    $repos = array_filter([$prog->jour_repos_1, $prog->jour_repos_2]);
@endphp

                                    <tr>
                                    @foreach($period as $date)
                                        @php
                                            $estDansLaPlage = $date->between($dateDebut, $dateFin);
                                            $estRepos = in_array($date->format('N'), $repos);
                                        @endphp

                                        <td class="p-2" style="{{ !$estDansLaPlage ? 'background-color: #f8f9fa; color: #ccc;' : '' }}">
                                            @if($estDansLaPlage)
                                                <div class="fw-bold fs-6 mb-1">{{ $date->format('d') }}</div>
                                                <span class="badge w-100 {{ $estRepos ? 'bg-danger' : 'bg-success' }}" style="font-size: 0.6rem;">
                                                    {{ $estRepos ? 'REPOS' : 'SERVICE' }}
                                                </span>
                                                <div class="text-muted" style="font-size: 0.55rem;">{{ $date->translatedFormat('M') }}</div>
                                            @else
                                                <div class="text-muted" style="opacity: 0.4;">{{ $date->format('d') }}</div>
                                            @endif
                                        </td>

                                        @if($date->format('N') == 7 && !$loop->last)
                                            </tr><tr>
                                        @endif
                                    @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<style>
@media print {
    body {
        background-color: #fff !important;
        font-size: 12px;
    }
    .no-print {
        display: none !important;
    }
    .container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    .card {
        border: 1px solid #dee2e6 !important;
        box-shadow: none !important;
    }
    .table {
        border-collapse: collapse !important;
    }
    .table th, .table td {
        border: 1px solid #dee2e6 !important;
    }
}
</style>
@endsection