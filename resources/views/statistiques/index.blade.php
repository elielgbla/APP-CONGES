@extends('layouts.app')

@section('content')
<div class="container-fluid my-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Tableau de Bord Statistique & Décisionnel</h2>
            <p class="text-muted small mb-0">Analyse en temps réel de la gestion des effectifs et des processus RH.</p>
        </div>
        <div class="bg-white px-3 py-2 rounded shadow-sm text-end border">
            <span class="small text-muted d-block">Date d'analyse</span>
            <strong class="text-primary">{{ now()->format('d/m/Y H:i') }}</strong>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle me-3">
                        <i class="fa-solid fa-users fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold d-block">Effectif Total</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $effectifTotal }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle me-3">
                        <i class="fa-solid fa-user-check fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold d-block">Agents Actifs</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $statsEmployes['actif'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle me-3">
                        <i class="fa-solid fa-hourglass-split fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold d-block">Délai de Validation</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $delaiTraitement ?? 0 }} <span class="fs-6 fw-normal text-muted">jours</span></h3>
                    </div>
                </div>
            </div>
        </div>

        @php
            $enAttente = ($statsDemandes['en_attente_n1'] ?? 0) + ($statsDemandes['en_attente_n2'] ?? 0) + ($statsDemandes['en_attente_n3'] ?? 0) + ($statsDemandes['en_attente_n4'] ?? 0);
        @endphp
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm p-3 bg-white h-100">
                <div class="d-flex align-items-center">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-circle me-3">
                        <i class="fa-solid fa-file-invoice fa-2x"></i>
                    </div>
                    <div>
                        <span class="text-muted small fw-semibold d-block">Demandes en Attente</span>
                        <h3 class="fw-bold text-dark mb-0">{{ $enAttente }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-bar me-2 text-primary"></i>Répartition des Effectifs par Service</h5>
                <div style="position: relative; height:300px;">
                    <canvas id="chartServices"></canvas>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm p-4 bg-white h-100">
                <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-pie me-2 text-success"></i>Statut Global des Demandes</h5>
                <div style="position: relative; height:300px;" class="d-flex justify-content-center align-items-center">
                    <canvas id="chartDemandes"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white h-100">
                <div class="mb-3">
                    <h5 class="fw-bold text-dark mb-1"><i class="fa-solid fa-cake-candles me-2 text-info"></i>Pyramide & Moyenne d'Âge</h5>
                    <span class="text-muted small">Moyenne d'âge calculée par direction sur les agents actifs.</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-top">
                        <thead class="table-light">
                            <tr>
                                <th>Service / Direction</th>
                                <th class="text-center">Moyenne d'Âge</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ageMoyenParService as $ageService)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $ageService->libelle_service }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-3 py-2 fw-bold fs-6">
                                            {{ $ageService->age_moyen }} ans
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted small py-3">Aucune donnée démographique disponible</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm p-4 bg-white h-100">
                <div class="mb-3">
                    <h5 class="fw-bold text-dark mb-1"><i class="fa-solid fa-calendar-minus me-2 text-danger"></i>Suivi de l'Absentéisme Théorique</h5>
                    <span class="text-muted small">Cumul des jours de permissions accordés et validés par CSP.</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-top">
                        <thead class="table-light">
                            <tr>
                                <th>Catégorie Professionnelle</th>
                                <th class="text-center">Jours d'Absences cumulés</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($joursAbsencesParCategorie as $abs)
                                <tr>
                                    <td class="fw-semibold text-dark">{{ $abs->libelle_categorie }}</td>
                                    <td class="text-center">
                                        <span class="text-danger fw-bold fs-6">
                                            {{ number_format($abs->total_jours, 0, ',', ' ') }} Jours
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted small py-3">Aucune absence approuvée enregistrée</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // --- Graphique 1 : Histogramme des effectifs par service ---
        const ctxServices = document.getElementById('chartServices').getContext('2d');
        new Chart(ctxServices, {
            type: 'bar',
            data: {
                labels: {!! json_encode($employesParService->pluck('libelle_service')) !!},
                datasets: [{
                    label: 'Nombre d\'agents actifs',
                    data: {!! json_encode($employesParService->pluck('total')) !!},
                    backgroundColor: 'rgba(13, 110, 253, 0.75)',
                    borderColor: 'rgb(13, 110, 253)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // --- Graphique 2 : Camembert Statut des demandes ---
        const ctxDemandes = document.getElementById('chartDemandes').getContext('2d');
        new Chart(ctxDemandes, {
            type: 'doughnut',
            data: {
                labels: ['Approuvées', 'Rejetées', 'En Attente'],
                datasets: [{
                    data: [
                        {{ $statsDemandes['approuvee'] ?? 0 }},
                        {{ $statsDemandes['rejetee'] ?? 0 }},
                        {{ $enAttente }}
                    ],
                    backgroundColor: [
                        '#2ea44f', // Vert
                        '#dc3545', // Rouge
                        '#ffc107'  // Jaune
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@endsection